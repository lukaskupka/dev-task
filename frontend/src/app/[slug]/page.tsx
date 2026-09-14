import BusinessPage from '@/components/BusinessPage';

interface Props {
  params: Promise<{ slug: string }>;
}

export default async function BusinessSlugPage({ params }: Props) {
  const { slug } = await params;
  return <BusinessPage slug={slug} />;
}
