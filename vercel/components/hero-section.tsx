'use client'

import { ArrowRight, Play } from 'lucide-react'
import { Button } from '@/components/ui/button'
import SeedlingAnimation from './seedling-animation'
import Link from 'next/link'

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'https://bibitgratis.com'

export default function HeroSection() {
  return (
    <section className="relative min-h-screen flex flex-col items-center justify-center py-24 lg:py-32 overflow-hidden">
      {/* Clean off-white background with subtle gradient */}
      <div className="absolute inset-0 bg-gradient-to-b from-[#fafcfa] via-[#f8faf8] to-white pointer-events-none" />
      
      {/* Very subtle decorative accents */}
      <div className="absolute top-32 right-20 w-96 h-96 bg-[#5a8f5a]/[0.03] rounded-full blur-3xl" />
      <div className="absolute bottom-32 left-20 w-80 h-80 bg-[#7cb87c]/[0.03] rounded-full blur-3xl" />
      
      {/* Content */}
      <div className="relative z-10 max-w-7xl mx-auto px-6 lg:px-12 w-full">
        <div className="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
          {/* Left side - Text content with generous spacing */}
          <div className="flex-1 text-center lg:text-left max-w-xl">
            {/* Badge */}
            <div className="inline-flex items-center gap-2 px-4 py-2 mb-10 rounded-full bg-[#f5f9f5] border border-[#e0efe0]">
              <span className="flex h-2 w-2 relative">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#5a8f5a] opacity-75" />
                <span className="relative inline-flex rounded-full h-2 w-2 bg-[#5a8f5a]" />
              </span>
              <span className="text-sm font-medium text-[#3d5a3d] tracking-wide">
                Platform Resmi Kementerian Kehutanan RI
              </span>
            </div>

            {/* Headline - Elegant Serif */}
            <h1 className="font-serif text-5xl sm:text-6xl lg:text-7xl font-semibold text-[#2d4a2d] leading-[1.08] tracking-tight mb-8">
              <span className="block">Ayo Tanam</span>
              <span className="block text-[#5a8f5a]">Pohon</span>
            </h1>
            
            <p className="text-lg lg:text-xl text-[#5a7a5a] max-w-md mx-auto lg:mx-0 leading-relaxed font-light mb-10">
              Platform resmi Kementerian Kehutanan untuk akses informasi dan distribusi bibit tanaman hutan gratis bagi masyarakat. Pantau stok, ajukan permintaan, dan berkontribusi untuk lingkungan.
            </p>

            {/* CTA Buttons */}
            <div className="flex flex-col sm:flex-row items-center lg:items-start justify-center lg:justify-start gap-4">
              <Button 
                size="lg"
                className="w-full sm:w-auto bg-[#5a8f5a] hover:bg-[#4a7f4a] text-white px-8 py-6 text-base rounded-xl shadow-lg shadow-[#5a8f5a]/15 hover:shadow-[#5a8f5a]/25 transition-all duration-300"
                asChild
              >
                <Link href={`${BACKEND_URL}/public/request-form`}>
                  Ajukan Permintaan Bibit
                  <ArrowRight className="ml-2 w-5 h-5" />
                </Link>
              </Button>
              
              <Button 
                variant="outline"
                size="lg"
                className="w-full sm:w-auto border-2 border-[#5a8f5a]/20 text-[#5a8f5a] hover:bg-[#5a8f5a]/5 hover:border-[#5a8f5a]/40 px-8 py-6 text-base rounded-xl transition-all duration-300"
                asChild
              >
                <Link href={`${BACKEND_URL}/public/stock-search`}>
                  <Play className="mr-2 w-5 h-5 fill-current" />
                  Cari Bibit Tersedia
                </Link>
              </Button>
            </div>

            {/* Trust stats inline */}
            <div className="mt-12 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm text-[#5a7a5a]">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-[#5a8f5a]" />
                <span>34 BPDAS Aktif</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-[#5a8f5a]" />
                <span>34 Provinsi Terjangkau</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-[#5a8f5a]" />
                <span>Bibit Bersertifikat</span>
              </div>
            </div>
          </div>

          {/* Right side - Animation with premium white space */}
          <div className="flex-1 flex items-center justify-center lg:justify-end">
            <div className="relative">
              {/* Elegant decorative ring */}
              <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div className="w-[440px] h-[440px] rounded-full border border-[#5a8f5a]/[0.06]" />
              </div>
              <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div className="w-[380px] h-[380px] rounded-full border border-[#5a8f5a]/[0.04]" />
              </div>
              
              <SeedlingAnimation />
            </div>
          </div>
        </div>
      </div>

      {/* Scroll indicator */}
      <div className="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2">
        <span className="text-xs text-[#5a8f5a]/50 uppercase tracking-[0.2em] font-medium">Gulir</span>
        <div className="w-5 h-9 rounded-full border border-[#5a8f5a]/20 flex justify-center pt-2">
          <div className="w-1 h-2 bg-[#5a8f5a]/40 rounded-full animate-bounce" />
        </div>
      </div>
    </section>
  )
}
