'use client'

import { Leaf, Globe, Users, Shield, Truck, TreePine } from 'lucide-react'

const features = [
  {
    icon: Leaf,
    title: 'Bibit Premium Berkualitas',
    description: 'Semua bibit melewati proses seleksi ketat dan sertifikasi untuk menjamin kualitas terbaik bagi penghijauan Indonesia.',
    accent: 'from-[#5a8f5a] to-[#7cb87c]'
  },
  {
    icon: Globe,
    title: 'Distribusi ke Seluruh Nusantara',
    description: 'Jangkauan ke seluruh 34 provinsi Indonesia dengan jaringan distribusi BPDAS yang terintegrasi dan efisien.',
    accent: 'from-[#4a9f6a] to-[#6ac88a]'
  },
  {
    icon: Users,
    title: 'Kolaborasi BPDAS & BPTH',
    description: 'Bekerja sama dengan Balai Pengelolaan DAS dan Balai Perbenihan Tanaman Hutan untuk pendampingan ahli di lapangan.',
    accent: 'from-[#3d8f6d] to-[#5ab88a]'
  },
  {
    icon: Shield,
    title: 'Bebas & Bersertifikat',
    description: 'Bibit didistribusikan secara gratis kepada masyarakat, kelompok tani, dan lembaga yang memenuhi persyaratan resmi.',
    accent: 'from-[#5a8f7a] to-[#7cb89a]'
  },
  {
    icon: Truck,
    title: 'Transparan & Akuntabel',
    description: 'Semua data distribusi tercatat secara digital dan dapat dipantau masyarakat untuk menjamin akuntabilitas program.',
    accent: 'from-[#4a8f5a] to-[#6cb87c]'
  },
  {
    icon: TreePine,
    title: 'Varietas Lengkap',
    description: 'Tersedia berbagai jenis bibit tanaman hutan, pohon penghasil buah, dan tanaman konservasi untuk berbagai kebutuhan.',
    accent: 'from-[#5a9f5a] to-[#7cc87c]'
  }
]

export default function FeaturesSection() {
  return (
    <section id="services" className="relative py-24 lg:py-32">
      {/* Subtle background accent */}
      <div className="absolute top-0 right-0 w-80 h-80 bg-[#5a8f5a]/[0.03] rounded-full blur-3xl" />

      <div className="relative max-w-7xl mx-auto px-6 lg:px-8">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="inline-block text-sm font-medium text-[#5a8f5a] uppercase tracking-widest mb-4">
            Keunggulan Kami
          </span>
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-semibold text-[#2d4a2d] mb-6 leading-tight">
            Layanan Unggulan
          </h2>
          <p className="text-lg text-[#5a7a5a] leading-relaxed">
            Komitmen kami untuk menyediakan bibit terbaik dan layanan prima demi mendukung Indonesia yang lebih hijau dan lestari.
          </p>
        </div>

        {/* Features Grid with Glassmorphism */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <div
              key={feature.title}
              className="group relative"
              style={{ animationDelay: `${index * 100}ms` }}
            >
              {/* Card */}
              <div className="relative h-full bg-white/70 backdrop-blur-sm rounded-2xl p-8 border border-[#e0efe0] hover:border-[#5a8f5a]/30 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                {/* Icon */}
                <div className="mb-6">
                  <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${feature.accent} flex items-center justify-center`}>
                    <feature.icon className="w-6 h-6 text-white" />
                  </div>
                </div>

                {/* Content */}
                <h3 className="font-serif text-xl font-semibold text-[#2d4a2d] mb-3">
                  {feature.title}
                </h3>
                <p className="text-[#5a7a5a] leading-relaxed">
                  {feature.description}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
