import Header from '@/components/header'
import HeroSection from '@/components/hero-section'
import StatsSection from '@/components/stats-section'
import FeaturesSection from '@/components/features-section'
import CTASection from '@/components/cta-section'
import NewsSection from '@/components/news-section'
import Footer from '@/components/footer'
import LeafBackground from '@/components/leaf-background'

export default function Home() {
  return (
    <main className="relative min-h-screen">
      <LeafBackground />
      <Header />
      <HeroSection />
      <StatsSection />
      <FeaturesSection />
      <CTASection />
      <NewsSection />
      <Footer />
    </main>
  )
}
