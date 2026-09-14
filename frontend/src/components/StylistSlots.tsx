'use client';

import { gql, useQuery } from '@apollo/client';
import { useState } from 'react';
import BookingForm, { type Slot } from './BookingForm';

const GET_STYLIST_SLOTS = gql`
  query GetStylistSlots($businessId: ID!, $serviceId: ID!, $date: String!) {
    business(id: $businessId) {
      services {
        edges {
          node {
            id
            stylists {
              edges {
                node {
                  id
                  availableSlots(serviceId: $serviceId, date: $date) {
                    edges {
                      node {
                        startTime
                        endTime
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
`;

interface GetStylistSlotsData {
  business: {
    services: {
      edges: {
        node: {
          id: string;
          stylists: {
            edges: {
              node: {
                id: string;
                availableSlots: { edges: { node: Slot }[] };
              };
            }[];
          };
        };
      }[];
    };
  } | null;
}

interface Props {
  businessId: string;
  stylistId: string;
  stylistName: string;
  serviceId: string;
  serviceName: string;
  date: string;
  onDateChange: (date: string) => void;
}

function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
}

export default function StylistSlots({ businessId, stylistId, stylistName, serviceId, serviceName, date, onDateChange }: Props) {
  const [selectedSlot, setSelectedSlot] = useState<string | null>(null);
  const [slotOverride, setSlotOverride] = useState<Slot[] | null>(null);

  const { data, loading, error } = useQuery<GetStylistSlotsData>(GET_STYLIST_SLOTS, {
    variables: { businessId, serviceId, date },
    onCompleted: () => setSlotOverride(null),
  });

  const serviceNode = data?.business?.services.edges.find((e) => e.node.id === serviceId)?.node;
  const stylistNode = serviceNode?.stylists.edges.find((e) => e.node.id === stylistId)?.node;
  const slots = slotOverride ?? stylistNode?.availableSlots.edges.map((e) => e.node) ?? [];

  return (
    <div className="mt-3 border-t border-stone-700 pt-3">
      <div className="flex items-center gap-3 mb-3">
        <label className="text-xs tracking-widest uppercase text-stone-500">Date</label>
        <input
          type="date"
          value={date}
          onChange={(e) => onDateChange(e.target.value)}
          className="bg-charcoal-900 border border-stone-700 text-stone-200 text-sm px-3 py-1.5 rounded-sm focus:outline-none focus:border-gold-500 [color-scheme:dark]"
        />
      </div>

      {loading && (
        <div className="flex items-center gap-2 text-stone-500 text-xs">
          <div className="w-3 h-3 border border-gold-500 border-t-transparent rounded-full animate-spin" />
          Loading slots…
        </div>
      )}

      {error && <p className="text-red-400 text-xs">{error.message}</p>}

      {!loading && !error && slots.length === 0 && (
        <p className="text-stone-500 text-xs italic">No available slots on this date.</p>
      )}

      {!loading && slots.length > 0 && (
        <div className="space-y-2">
          <div className="flex flex-wrap gap-2">
            {slots.map((slot) => (
              <button
                key={slot.startTime}
                onClick={() => setSelectedSlot((prev) => (prev === slot.startTime ? null : slot.startTime))}
                className={`px-3 py-1.5 border text-sm rounded-sm transition-colors ${
                  selectedSlot === slot.startTime
                    ? 'border-gold-500 text-gold-400 bg-charcoal-900'
                    : 'border-stone-700 text-stone-300 bg-charcoal-900 hover:border-gold-500 hover:text-gold-400'
                }`}
              >
                {formatTime(slot.startTime)}
              </button>
            ))}
          </div>

          {selectedSlot && (
            <BookingForm
              stylistId={stylistId}
              serviceId={serviceId}
              startTime={selectedSlot}
              date={date}
              stylistName={stylistName}
              serviceName={serviceName}
              onCancel={() => setSelectedSlot(null)}
              onSuccess={(freshSlots) => setSlotOverride(freshSlots)}
            />
          )}
        </div>
      )}
    </div>
  );
}
