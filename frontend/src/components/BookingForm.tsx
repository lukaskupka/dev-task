'use client';

import { gql, useMutation } from '@apollo/client';
import { useState } from 'react';

const CREATE_BOOKING = gql`
  mutation CreateBooking($input: CreateBookingInput!, $serviceId: ID!, $date: String!) {
    createBooking(input: $input) {
      stylist {
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
      errors {
        field
        message
      }
    }
  }
`;

export interface Slot {
  startTime: string;
  endTime: string;
}

interface CreateBookingData {
  createBooking: {
    stylist: {
      id: string;
      availableSlots: { edges: { node: Slot }[] };
    } | null;
    errors: { field: string | null; message: string }[];
  };
}

interface Props {
  stylistId: string;
  serviceId: string;
  startTime: string;
  date: string;
  stylistName: string;
  serviceName: string;
  onCancel: () => void;
  onSuccess: (slots: Slot[]) => void;
}

function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
}

export default function BookingForm({
  stylistId,
  serviceId,
  startTime,
  date,
  stylistName,
  serviceName,
  onCancel,
  onSuccess,
}: Props) {
  const [customerName, setCustomerName] = useState('');
  const [customerContact, setCustomerContact] = useState('');
  const [contactError, setContactError] = useState('');
  const [serverErrors, setServerErrors] = useState<string[]>([]);
  const [succeeded, setSucceeded] = useState(false);

  const [createBooking, { loading }] = useMutation<CreateBookingData>(CREATE_BOOKING);

  function validateContact(value: string): string {
    const email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phone = /^\+?[\d\s\-().]{7,}$/;
    if (!email.test(value) && !phone.test(value)) {
      return 'Enter a valid email address or phone number.';
    }
    return '';
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const err = validateContact(customerContact);
    setContactError(err);
    if (err) return;
    setServerErrors([]);

    const variables = {
      variables: {
        input: { stylistId, serviceId, startTime, customerName, customerContact },
        serviceId,
        date,
      },
    };

    // Simulate double-click: fire two identical requests concurrently
    const [{ data }] = await Promise.all([
      createBooking(variables),
      createBooking(variables),
    ]);

    const errors = data?.createBooking.errors ?? [];
    if (errors.length > 0) {
      setServerErrors(errors.map((err) => err.message));
      return;
    }

    const slots = data?.createBooking.stylist?.availableSlots.edges.map((e) => e.node) ?? [];
    onSuccess(slots);
    setSucceeded(true);
  }

  if (succeeded) {
    return (
      <div className="mt-3 p-4 border border-stone-700 bg-charcoal-900 rounded-sm flex items-center justify-between gap-4">
        <p className="text-gold-400 text-sm tracking-wide">Booking requested.</p>
        <button
          onClick={onCancel}
          className="px-4 py-1.5 border border-stone-700 hover:border-stone-500 text-stone-400 text-sm rounded-sm transition-colors shrink-0"
        >
          OK
        </button>
      </div>
    );
  }

  return (
    <form
      onSubmit={handleSubmit}
      className="mt-3 p-4 border border-stone-700 bg-charcoal-900 rounded-sm space-y-4"
    >
      <div className="text-xs text-stone-500 space-y-0.5">
        <p>
          <span className="text-stone-400">{serviceName}</span> with{' '}
          <span className="text-stone-400">{stylistName}</span>
        </p>
        <p>
          <span className="text-gold-500">{formatTime(startTime)}</span>
        </p>
      </div>

      <div className="space-y-3">
        <div>
          <label className="block text-xs tracking-widest uppercase text-stone-500 mb-1">
            Your name
          </label>
          <input
            type="text"
            required
            value={customerName}
            onChange={(e) => setCustomerName(e.target.value)}
            className="w-full bg-charcoal-950 border border-stone-700 text-stone-200 text-sm px-3 py-2 rounded-sm focus:outline-none focus:border-gold-500"
          />
        </div>

        <div>
          <label className="block text-xs tracking-widest uppercase text-stone-500 mb-1">
            Phone / email
          </label>
          <input
            type="text"
            required
            value={customerContact}
            onChange={(e) => { setCustomerContact(e.target.value); setContactError(''); }}
            className={`w-full bg-charcoal-950 border text-stone-200 text-sm px-3 py-2 rounded-sm focus:outline-none ${
              contactError ? 'border-red-500 focus:border-red-500' : 'border-stone-700 focus:border-gold-500'
            }`}
          />
          {contactError && <p className="text-red-400 text-xs mt-1">{contactError}</p>}
        </div>
      </div>

      {serverErrors.length > 0 && (
        <ul className="space-y-1">
          {serverErrors.map((msg) => (
            <li key={msg} className="text-red-400 text-xs">{msg}</li>
          ))}
        </ul>
      )}

      <div className="flex gap-3 pt-1">
        <button
          type="submit"
          disabled={loading}
          className="flex-1 py-2 bg-gold-600 hover:bg-gold-500 disabled:opacity-50 text-charcoal-950 text-sm font-medium tracking-wide rounded-sm transition-colors"
        >
          {loading ? 'Booking…' : 'Book'}
        </button>
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-2 border border-stone-700 hover:border-stone-500 text-stone-400 text-sm rounded-sm transition-colors"
        >
          Cancel
        </button>
      </div>
    </form>
  );
}
