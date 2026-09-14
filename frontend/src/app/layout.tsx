import type { Metadata } from 'next';
import type { ReactNode } from 'react';
import AppApolloProvider from '@/components/ApolloProvider';
import './globals.css';

export const metadata: Metadata = {
  title: 'Developer Task',
};

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="en">
      <body className="bg-[#080808] text-stone-200">
        <nav className="border-b border-stone-800 bg-charcoal-950/80 backdrop-blur-sm sticky top-0 z-10">
          <div className="max-w-4xl mx-auto px-6 h-10 flex items-center justify-end">
            <a
              href="/business-panel"
              className="text-xs tracking-widest uppercase text-stone-500 hover:text-gold-400 transition-colors"
            >
              Business Panel
            </a>
          </div>
        </nav>
        <AppApolloProvider>{children}</AppApolloProvider>
      </body>
    </html>
  );
}
