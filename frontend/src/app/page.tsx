'use client';

import { gql, useQuery } from '@apollo/client';
import Link from 'next/link';

const GET_BUSINESSES = gql`
  query GetBusinesses {
    businesses {
      edges {
        node {
          id
          name
          slug
          services {
            edges {
              node {
                id
              }
            }
          }
          stylists {
            edges {
              node {
                id
              }
            }
          }
        }
      }
    }
  }
`;

interface Business {
  id: string;
  name: string;
  slug: string;
  services: { edges: { node: { id: string } }[] };
  stylists: { edges: { node: { id: string } }[] };
}

interface GetBusinessesData {
  businesses: { edges: { node: Business }[] };
}

export default function HomePage() {
  const { data, loading, error } = useQuery<GetBusinessesData>(GET_BUSINESSES);

  return (
    <div className="min-h-screen bg-charcoal-950 text-stone-200">
      <header className="border-b border-stone-800">
        <div className="max-w-4xl mx-auto px-6 py-16 text-center">
          <div className="flex items-center justify-center gap-3 mb-4">
            <div className="h-px w-12 bg-gold-500" />
            <span className="text-gold-500 text-xs tracking-[0.3em] uppercase font-medium">
              Book your visit
            </span>
            <div className="h-px w-12 bg-gold-500" />
          </div>
          <h1 className="font-serif text-4xl md:text-5xl font-light tracking-wide text-stone-100">
            Our Barbershops
          </h1>
        </div>
      </header>

      <main className="max-w-4xl mx-auto px-6 py-12">
        {loading && (
          <div className="flex items-center justify-center gap-3 py-16">
            <div className="w-6 h-6 border-2 border-gold-500 border-t-transparent rounded-full animate-spin" />
            <span className="text-stone-400 text-sm tracking-widest uppercase">Loading</span>
          </div>
        )}

        {error && <p className="text-red-400 text-sm">{error.message}</p>}

        {!loading && !error && (
          <div className="grid gap-px">
            {data?.businesses.edges.map(({ node: business }) => {
              const serviceCount = business.services.edges.length;
              const stylistCount = business.stylists.edges.length;

              return (
                <Link
                  key={business.id}
                  href={`/${business.slug}`}
                  className="group border border-stone-800 bg-charcoal-900 px-8 py-7 flex items-center justify-between hover:bg-stone-800/40 transition-colors"
                >
                  <div className="flex items-center gap-5">
                    <span className="w-1 h-10 bg-gold-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0" />
                    <div>
                      <p className="font-serif text-xl font-light tracking-wide text-stone-100 group-hover:text-gold-300 transition-colors">
                        {business.name}
                      </p>
                      <p className="text-xs text-stone-500 mt-1">
                        {serviceCount} {serviceCount === 1 ? 'service' : 'services'} · {stylistCount} {stylistCount === 1 ? 'stylist' : 'stylists'}
                      </p>
                    </div>
                  </div>
                  <span className="text-stone-600 group-hover:text-gold-500 transition-colors text-lg">→</span>
                </Link>
              );
            })}
          </div>
        )}
      </main>
    </div>
  );
}
