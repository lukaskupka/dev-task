'use client';

import { ApolloProvider } from '@apollo/client';
import { createApolloClient } from '@/lib/apollo-client';
import { useMemo, type ReactNode } from 'react';

export default function AppApolloProvider({ children }: { children: ReactNode }) {
  const client = useMemo(() => createApolloClient(), []);
  return <ApolloProvider client={client}>{children}</ApolloProvider>;
}
