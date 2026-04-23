'use client'

import { ArrowRight, Search, TreeDeciduous } from 'lucide-react'
import { Button } from '@/components/ui/button'
import Link from 'next/link'

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'https://bibitgratis.com'

export default function CTASection() {
  return (
    <section className="relative py-24 lg:py-32 overflow-hidden">
      {/* Background */}
      <div className="absolute inset-0 bg-gradient-to-br from-[#5a8f5a] via-[#4a7f4a] to-[#3d6b3d]" />
      
      {/* Simple decorative accent */}
      <div className="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-3xl" />
      <div className="absolute bottom-0 right-0 w-80 h-80 bg-white/5 rounded-full blur-3xl" />

      {/* Decorative leaf SVG */}
      <svg className="absolute top-0 right-0 w-64 h-64 text-white/5" viewBox="0 0 200 200" aria-hidden="true">
        <path d="M200 0 Q120 40, 100 100 Q80 160, 0 200 L200 200 Z" fill="currentColor" />
      </svg>

      <div className="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div className="text-center max-w-4xl mx-auto">
          {/* Icon */}
          <div className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-sm mb-8">
            <TreeDeciduous className="w-10 h-10 text-white" />
          </div>

          {/* Headline */}
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-semibold text-white mb-6 leading-tight">
            Mulai Berkontribusi untuk{' '}
            <span className="text-[#c8f0c8]">Indonesia Hijau</span>
          </h2>

          <p className="text-lg md:text-xl text-white/80 mb-10 max-w-2xl mx-auto leading-relaxed">
            Dapatkan bibit berkualitas gratis dan jadilah bagian dari gerakan penghijauan nasional. 
            Setiap pohon yang Anda tanam adalah warisan untuk generasi mendatang.
          </p>

          {/* CTA Buttons */}
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
            <Button 
              size="lg"
              className="w-full sm:w-auto bg-white text-[#3d6b3d] hover:bg-[#f0fff0] px-8 py-6 text-lg font-semibold shadow-xl shadow-black/20 hover:shadow-2xl hover:shadow-black/30 hover:scale-105 transition-all duration-300"
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
              className="w-full sm:w-auto border-2 border-white/30 text-white hover:bg-white/10 backdrop-blur-sm px-8 py-6 text-lg transition-all duration-300"
              asChild
            >
              <Link href={`${BACKEND_URL}/public/stock-search`}>
                <Search className="mr-2 w-5 h-5" />
                Cari Bibit Tersedia
              </Link>
            </Button>
          </div>

          {/* Trust indicators */}
          <div className="mt-12 pt-12 border-t border-white/20">
            <p className="text-white/60 text-sm mb-6 uppercase tracking-wider">Didukung oleh</p>
            <div className="flex flex-wrap items-center justify-center gap-8 text-white/80">
              <div className="flex items-center gap-2">
                <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                  <span className="font-serif font-bold text-lg">K</span>
                </div>
                <span className="text-sm">Kementerian LHK RI</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                  <span className="font-serif font-bold text-lg">B</span>
                </div>
                <span className="text-sm">BPDAS Seluruh Indonesia</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                  <span className="font-serif font-bold text-lg">P</span>
                </div>
                <span className="text-sm">Pemda 34 Provinsi</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
