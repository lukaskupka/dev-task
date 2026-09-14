'use client';

import { gql, useQuery } from '@apollo/client';
import { useState } from 'react';
import StylistSlots from './StylistSlots';

const GET_BUSINESS_BY_SLUG = gql`
  query GetBusinessBySlug($slug: String!) {
    businessBySlug(slug: $slug) {
      id
      name
      services {
        edges {
          node {
            id
            name
            durationMinutes
            price
            currency
            stylists {
              edges {
                node {
                  id
                  name
                  photoUrl
                }
              }
            }
          }
        }
      }
    }
  }
`;

interface Stylist {
  id: string;
  name: string;
  photoUrl: string | null;
}

interface Service {
  id: string;
  name: string;
  durationMinutes: number;
  price: number;
  currency: string;
  stylists: { edges: { node: Stylist }[] };
}

interface GetBusinessData {
  businessBySlug: {
    id: string;
    name: string;
    services: { edges: { node: Service }[] };
  } | null;
}

interface Props {
  slug: string;
}

function todayString(): string {
  return new Date().toISOString().slice(0, 10);
}

export default function BusinessPage({ slug }: Props) {
  const [selectedServiceId, setSelectedServiceId] = useState<string | null>(null);
  const [selectedStylistId, setSelectedStylistId] = useState<string | null>(null);
  const [selectedDate, setSelectedDate] = useState<string>(todayString);

  const { data, loading, error } = useQuery<GetBusinessData>(GET_BUSINESS_BY_SLUG, {
    variables: { slug },
  });

  if (loading) {
    return (
      <div className="min-h-screen bg-charcoal-950 flex items-center justify-center">
        <div className="flex flex-col items-center gap-4">
          <div className="w-10 h-10 border-2 border-gold-500 border-t-transparent rounded-full animate-spin" />
          <p className="text-stone-400 text-sm tracking-widest uppercase">Loading</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-charcoal-950 flex items-center justify-center">
        <p className="text-red-400 text-sm">{error.message}</p>
      </div>
    );
  }

  const business = data?.businessBySlug;
  if (!business) {
    return (
      <div className="min-h-screen bg-charcoal-950 flex items-center justify-center">
        <p className="text-stone-400">Business not found.</p>
      </div>
    );
  }

  const services = business.services.edges.map((e) => e.node);

  function toggleService(id: string) {
    setSelectedServiceId((prev) => {
      if (prev === id) {
        setSelectedStylistId(null);
        return null;
      }
      setSelectedStylistId(null);
      return id;
    });
  }

  return (
    <div className="min-h-screen bg-charcoal-950 text-stone-200">
      {/* Hero header */}
      <header className="relative border-b border-stone-800">
        <div className="absolute inset-0 bg-gradient-to-b from-charcoal-900 to-charcoal-950" />
        <div className="relative max-w-4xl mx-auto px-6 py-16 text-center">
          <div className="flex items-center justify-center gap-3 mb-4">
            <div className="h-px w-12 bg-gold-500" />
            <span className="text-gold-500 text-xs tracking-[0.3em] uppercase font-medium">
              Premium Barbershop
            </span>
            <div className="h-px w-12 bg-gold-500" />
          </div>
          <h1 className="font-serif text-4xl md:text-5xl font-light tracking-wide text-stone-100">
            {business.name}
          </h1>
        </div>
      </header>

      <main className="max-w-4xl mx-auto px-6 py-12">
        <section>
          <h2 className="text-xs tracking-[0.3em] uppercase text-gold-500 font-medium mb-8">
            Our Services
          </h2>

          <div className="space-y-px">
            {services.map((service) => {
              const serviceOpen = selectedServiceId === service.id;
              const stylists = service.stylists.edges.map((e) => e.node);

              return (
                <div key={service.id} className="border border-stone-800 bg-charcoal-900">
                  {/* Service row */}
                  <button
                    onClick={() => toggleService(service.id)}
                    className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-stone-800/40 transition-colors group"
                  >
                    <div className="flex items-center gap-4">
                      <span className="w-1 h-6 bg-gold-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0" />
                      <div>
                        <p className="font-medium text-stone-100 tracking-wide">{service.name}</p>
                        <p className="text-xs text-stone-500 mt-0.5">{service.durationMinutes} min</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-6">
                      <span className="text-gold-400 font-light text-lg">
                        {service.price} {service.currency}
                      </span>
                      <span
                        className={`text-stone-500 transition-transform duration-200 ${
                          serviceOpen ? 'rotate-180' : ''
                        }`}
                      >
                        ▾
                      </span>
                    </div>
                  </button>

                  {/* Stylists tabs */}
                  {serviceOpen && (() => {
                    const activeStylist = stylists.find((s) => s.id === selectedStylistId) ?? stylists[0] ?? null;
                    return (
                      <div className="border-t border-stone-800 bg-charcoal-950/60">
                        {/* Tab bar */}
                        <div className="flex border-b border-stone-800">
                          {stylists.map((stylist) => {
                            const active = activeStylist?.id === stylist.id;
                            return (
                              <button
                                key={stylist.id}
                                onClick={() => setSelectedStylistId(stylist.id)}
                                className={`flex items-center gap-2.5 px-5 py-3.5 text-sm transition-colors border-b-2 -mb-px ${
                                  active
                                    ? 'border-gold-500 text-gold-400'
                                    : 'border-transparent text-stone-400 hover:text-stone-200'
                                }`}
                              >
                                {stylist.photoUrl ? (
                                  <img
                                    src={stylist.photoUrl}
                                    alt={stylist.name}
                                    width={24}
                                    height={24}
                                    className="rounded-full object-cover w-6 h-6"
                                  />
                                ) : (
                                  <div className="w-6 h-6 rounded-full bg-stone-700 flex items-center justify-center text-stone-400 text-xs font-medium shrink-0">
                                    {stylist.name.charAt(0).toUpperCase()}
                                  </div>
                                )}
                                {stylist.name}
                              </button>
                            );
                          })}
                        </div>

                        {/* Tab content */}
                        {activeStylist && (
                          <div className="px-6 py-5">
                            <StylistSlots
                              businessId={business.id}
                              stylistId={activeStylist.id}
                              stylistName={activeStylist.name}
                              serviceId={service.id}
                              serviceName={service.name}
                              date={selectedDate}
                              onDateChange={setSelectedDate}
                            />
                          </div>
                        )}
                      </div>
                    );
                  })()}
                </div>
              );
            })}
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="border-t border-stone-800 mt-16">
        <div className="max-w-4xl mx-auto px-6 py-6 flex items-center justify-center gap-3">
          <div className="h-px w-8 bg-stone-700" />
          <span className="text-stone-600 text-xs tracking-widest uppercase">{business.name}</span>
          <div className="h-px w-8 bg-stone-700" />
        </div>
      </footer>
    </div>
  );
}
