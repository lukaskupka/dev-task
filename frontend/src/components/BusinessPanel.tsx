'use client';

import { gql, useMutation, useQuery } from '@apollo/client';
import { useState } from 'react';

const GET_BUSINESS_BOOKINGS = gql`
  query GetBusinessBookings($businessId: ID!) {
    businessBookings(businessId: $businessId) {
      edges {
        node {
          id
          startTime
          endTime
          status
          customerName
          customerContact
          service {
            id
            name
          }
          stylist {
            id
            name
          }
        }
      }
    }
  }
`;

const CONFIRM_BOOKING = gql`
  mutation ConfirmBooking($input: ConfirmBookingInput!) {
    confirmBooking(input: $input) {
      booking {
        id
        status
      }
      errors {
        message
      }
    }
  }
`;

const REJECT_BOOKING = gql`
  mutation RejectBooking($input: RejectBookingInput!) {
    rejectBooking(input: $input) {
      booking {
        id
        status
      }
      errors {
        message
      }
    }
  }
`;

interface Booking {
  id: string;
  startTime: string;
  endTime: string;
  status: 'PENDING' | 'CONFIRMED' | 'REJECTED';
  customerName: string;
  customerContact: string;
  service: { id: string; name: string };
  stylist: { id: string; name: string };
}

interface GetBusinessBookingsData {
  businessBookings: { edges: { node: Booking }[] };
}

interface MutationData {
  confirmBooking?: { booking: { id: string; status: string } | null; errors: { message: string }[] };
  rejectBooking?: { booking: { id: string; status: string } | null; errors: { message: string }[] };
}

interface Props {
  businessId: string;
}

function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
}

function formatDate(iso: string): string {
  return new Date(iso).toLocaleDateString('cs-CZ', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

function dateKey(iso: string): string {
  return iso.slice(0, 10);
}

const STATUS_STYLES: Record<string, string> = {
  PENDING:   'bg-stone-700 text-stone-300',
  CONFIRMED: 'bg-emerald-900/60 text-emerald-400',
  REJECTED:  'bg-red-900/40 text-red-400',
};

const STATUS_LABELS: Record<string, string> = {
  PENDING:   'Pending',
  CONFIRMED: 'Confirmed',
  REJECTED:  'Rejected',
};

export default function BusinessPanel({ businessId }: Props) {
  const [localStatuses, setLocalStatuses] = useState<Record<string, Booking['status']>>({});
  const [actionErrors, setActionErrors] = useState<Record<string, string>>({});

  const { data, loading, error } = useQuery<GetBusinessBookingsData>(GET_BUSINESS_BOOKINGS, {
    variables: { businessId },
  });

  const [confirmBooking, { loading: confirming }] = useMutation<MutationData>(CONFIRM_BOOKING);
  const [rejectBooking, { loading: rejecting }] = useMutation<MutationData>(REJECT_BOOKING);

  async function handleConfirm(booking: Booking) {
    setActionErrors((prev) => ({ ...prev, [booking.id]: '' }));
    const { data: res } = await confirmBooking({
      variables: { input: { bookingId: booking.id, stylistId: booking.stylist.id } },
    });
    const errors = res?.confirmBooking?.errors ?? [];
    if (errors.length > 0) {
      setActionErrors((prev) => ({ ...prev, [booking.id]: errors[0].message }));
      return;
    }
    const newStatus = res?.confirmBooking?.booking?.status as Booking['status'];
    if (newStatus) setLocalStatuses((prev) => ({ ...prev, [booking.id]: newStatus }));
  }

  async function handleReject(booking: Booking) {
    setActionErrors((prev) => ({ ...prev, [booking.id]: '' }));
    const { data: res } = await rejectBooking({
      variables: { input: { bookingId: booking.id, stylistId: booking.stylist.id } },
    });
    const errors = res?.rejectBooking?.errors ?? [];
    if (errors.length > 0) {
      setActionErrors((prev) => ({ ...prev, [booking.id]: errors[0].message }));
      return;
    }
    const newStatus = res?.rejectBooking?.booking?.status as Booking['status'];
    if (newStatus) setLocalStatuses((prev) => ({ ...prev, [booking.id]: newStatus }));
  }

  if (loading) {
    return (
      <div className="flex items-center gap-2 text-stone-500 text-sm py-8">
        <div className="w-4 h-4 border border-gold-500 border-t-transparent rounded-full animate-spin" />
        Loading bookings…
      </div>
    );
  }

  if (error) return <p className="text-red-400 text-sm">{error.message}</p>;

  const bookings = (data?.businessBookings.edges.map((e) => e.node) ?? []).map((b) => ({
    ...b,
    status: (localStatuses[b.id] ?? b.status) as Booking['status'],
  }));

  // Group by date
  const byDay = bookings.reduce<Record<string, Booking[]>>((acc, b) => {
    const key = dateKey(b.startTime);
    (acc[key] ??= []).push(b);
    return acc;
  }, {});

  const sortedDays = Object.keys(byDay).sort();

  if (sortedDays.length === 0) {
    return <p className="text-stone-500 text-sm italic py-8">No bookings yet.</p>;
  }

  return (
    <div className="space-y-10">
      {sortedDays.map((day) => (
        <section key={day}>
          <h3 className="text-xs tracking-[0.25em] uppercase text-gold-500 font-medium mb-4 capitalize">
            {formatDate(day)}
          </h3>

          <div className="space-y-px">
            {byDay[day].map((booking) => {
              const isPending = booking.status === 'PENDING';
              const busy = confirming || rejecting;

              return (
                <div
                  key={booking.id}
                  className="border border-stone-800 bg-charcoal-900 px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-4"
                >
                  {/* Time */}
                  <div className="text-gold-400 font-light text-lg tabular-nums shrink-0 w-28">
                    {formatTime(booking.startTime)}–{formatTime(booking.endTime)}
                  </div>

                  {/* Details */}
                  <div className="flex-1 min-w-0">
                    <p className="font-medium text-stone-100 text-sm">{booking.customerName}</p>
                    <p className="text-xs text-stone-500 mt-0.5">
                      {booking.service.name} · {booking.stylist.name} · {booking.customerContact}
                    </p>
                    {actionErrors[booking.id] && (
                      <p className="text-red-400 text-xs mt-1">{actionErrors[booking.id]}</p>
                    )}
                  </div>

                  {/* Status / actions */}
                  <div className="flex items-center gap-2 shrink-0">
                    {isPending ? (
                      <>
                        <button
                          onClick={() => handleConfirm(booking)}
                          disabled={busy}
                          className="px-3 py-1.5 bg-emerald-900/60 hover:bg-emerald-800/60 disabled:opacity-50 border border-emerald-700 text-emerald-400 text-xs rounded-sm transition-colors"
                        >
                          Confirm
                        </button>
                        <button
                          onClick={() => handleReject(booking)}
                          disabled={busy}
                          className="px-3 py-1.5 bg-red-900/40 hover:bg-red-900/60 disabled:opacity-50 border border-red-800 text-red-400 text-xs rounded-sm transition-colors"
                        >
                          Reject
                        </button>
                      </>
                    ) : (
                      <span className={`px-2.5 py-1 text-xs rounded-sm ${STATUS_STYLES[booking.status]}`}>
                        {STATUS_LABELS[booking.status]}
                      </span>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </section>
      ))}
    </div>
  );
}
