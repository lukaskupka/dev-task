'use client';

import { useState } from 'react';
import BusinessPanel from '@/components/BusinessPanel';

const BUSINESSES = [
  { id: '11111111-1111-1111-1111-111111111111', name: "Gentlemen's Cut" },
  { id: '22222222-2222-2222-2222-222222222222', name: 'Luxury Barber Studio' },
];

function LoginForm({ onSuccess }: { onSuccess: () => void }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (username === 'admin' && password === 'barber') {
      onSuccess();
    } else {
      setError('Invalid username or password.');
    }
  }

  return (
    <div className="min-h-screen bg-charcoal-950 flex items-center justify-center px-4">
      <div className="w-full max-w-sm">
        <div className="flex items-center justify-center gap-3 mb-6">
          <div className="h-px w-10 bg-gold-500" />
          <span className="text-gold-500 text-xs tracking-[0.3em] uppercase font-medium">Business Panel</span>
          <div className="h-px w-10 bg-gold-500" />
        </div>

        <form
          onSubmit={handleSubmit}
          className="border border-stone-800 bg-charcoal-900 p-8 space-y-5"
        >
          <div>
            <label className="block text-xs tracking-widest uppercase text-stone-500 mb-1">
              Username
            </label>
            <input
              type="text"
              required
              autoFocus
              value={username}
              onChange={(e) => { setUsername(e.target.value); setError(''); }}
              className="w-full bg-charcoal-950 border border-stone-700 text-stone-200 text-sm px-3 py-2 rounded-sm focus:outline-none focus:border-gold-500"
            />
          </div>

          <div>
            <label className="block text-xs tracking-widest uppercase text-stone-500 mb-1">
              Password
            </label>
            <input
              type="password"
              required
              value={password}
              onChange={(e) => { setPassword(e.target.value); setError(''); }}
              className={`w-full bg-charcoal-950 border text-stone-200 text-sm px-3 py-2 rounded-sm focus:outline-none ${
                error ? 'border-red-500 focus:border-red-500' : 'border-stone-700 focus:border-gold-500'
              }`}
            />
            {error && <p className="text-red-400 text-xs mt-1">{error}</p>}
          </div>

          <button
            type="submit"
            className="w-full py-2 bg-gold-600 hover:bg-gold-500 text-charcoal-950 text-sm font-medium tracking-wide rounded-sm transition-colors"
          >
            Sign in
          </button>
        </form>
      </div>
    </div>
  );
}

export default function BusinessPanelPage() {
  const [authed, setAuthed] = useState(false);
  const [activeId, setActiveId] = useState(BUSINESSES[0].id);

  if (!authed) {
    return <LoginForm onSuccess={() => setAuthed(true)} />;
  }

  return (
    <div className="min-h-screen bg-charcoal-950 text-stone-200">
      <header className="border-b border-stone-800">
        <div className="max-w-4xl mx-auto px-6 py-8 flex items-end justify-between">
          <div>
            <div className="flex items-center gap-3 mb-1">
              <div className="h-px w-8 bg-gold-500" />
              <span className="text-gold-500 text-xs tracking-[0.3em] uppercase font-medium">Business Panel</span>
            </div>
            <h1 className="font-serif text-3xl font-light tracking-wide text-stone-100">Bookings</h1>
          </div>
          <button
            onClick={() => setAuthed(false)}
            className="text-xs tracking-widest uppercase text-stone-500 hover:text-stone-300 transition-colors pb-1"
          >
            Sign out
          </button>
        </div>
      </header>

      <main className="max-w-4xl mx-auto px-6 py-10">
        <div className="flex border-b border-stone-800 mb-8">
          {BUSINESSES.map((b) => (
            <button
              key={b.id}
              onClick={() => setActiveId(b.id)}
              className={`px-5 py-3 text-sm transition-colors border-b-2 -mb-px ${
                activeId === b.id
                  ? 'border-gold-500 text-gold-400'
                  : 'border-transparent text-stone-400 hover:text-stone-200'
              }`}
            >
              {b.name}
            </button>
          ))}
        </div>

        <BusinessPanel key={activeId} businessId={activeId} />
      </main>
    </div>
  );
}
