import { ApolloClient, HttpLink, InMemoryCache } from '@apollo/client';

export function createApolloClient(): ApolloClient<unknown> {
  return new ApolloClient({
    link: new HttpLink({
      uri: process.env.NEXT_PUBLIC_GRAPHQL_URL,
    }),
    cache: new InMemoryCache(),
  });
}
