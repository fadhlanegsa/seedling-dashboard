'use client'

import { useEffect, useState } from 'react'
import { ArrowRight, Calendar, MapPin } from 'lucide-react'
import { Button } from '@/components/ui/button'
import Link from 'next/link'

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'https://bibitgratis.com'

// API news item shape (mirrors PHP model output)
interface ApiNewsItem {
  id: number
  title: string
  content: string
  source_type: 'pusat' | 'bpdas' | 'bpth'
  bpdas_name?: string
  image_filename?: string
  published_at: string
}

const sourceColors: Record<string, string> = {
  pusat: 'from-[#5a8f5a] to-[#4a7f4a]',
  bpdas: 'from-[#4a9f6a] to-[#3d8f5d]',
  bpth: 'from-[#5a8f7a] to-[#4a7f6a]',
}

const sourcePillColors: Record<string, string> = {
  pusat: 'bg-emerald-600',
  bpdas: 'bg-teal-600',
  bpth: 'bg-green-700',
}

const tabFilters = ['Semua', 'Pusat', 'BPDAS', 'BPTH']

function stripHtml(html: string): string {
  return html.replace(/<[^>]*>?/gm, '')
}

function truncate(text: string, maxLen = 110): string {
  return text.length > maxLen ? text.slice(0, maxLen).trimEnd() + '...' : text
}

function formatDate(dateStr: string): string {
  try {
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric', month: 'short', year: 'numeric',
    })
  } catch {
    return dateStr
  }
}

export default function NewsSection() {
  const [newsItems, setNewsItems] = useState<ApiNewsItem[]>([])
  const [loading, setLoading] = useState(true)
  const [activeTab, setActiveTab] = useState('Semua')

  useEffect(() => {
    fetch(`${BACKEND_URL}/public/api-landing-data`)
      .then((res) => res.json())
      .then((result) => {
        if (result.success && Array.isArray(result.data?.news)) {
          setNewsItems(result.data.news)
        }
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const filtered = newsItems.filter((n) => {
    if (activeTab === 'Semua') return true
    return n.source_type.toLowerCase() === activeTab.toLowerCase()
  })

  const sourceLabel = (item: ApiNewsItem) => {
    if (item.source_type === 'pusat') return 'Pusat'
    return item.bpdas_name || item.source_type.toUpperCase()
  }

  return (
    <section id="news" className="relative py-24 lg:py-32 bg-gradient-to-b from-transparent via-[#f5faf5] to-transparent">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        {/* Section Header */}
        <div className="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
          <div className="max-w-2xl">
            <span className="inline-block text-sm font-medium text-[#5a8f5a] uppercase tracking-widest mb-4">
              Informasi Terkini
            </span>
            <h2 className="font-serif text-4xl md:text-5xl font-semibold text-[#2d4a2d] mb-4 leading-tight">
              Kabar Kehutanan
            </h2>
            <p className="text-lg text-[#5a7a5a]">
              Berita dan informasi terbaru seputar penghijauan dari seluruh Indonesia.
            </p>
          </div>
          
          <Button variant="outline" className="w-fit border-[#5a8f5a] text-[#5a8f5a] hover:bg-[#5a8f5a] hover:text-white" asChild>
            <Link href={`${BACKEND_URL}/public/kabar-kehutanan`}>
              Lihat Semua Berita
              <ArrowRight className="ml-2 w-4 h-4" />
            </Link>
          </Button>
        </div>

        {/* Category tabs */}
        <div className="flex flex-wrap gap-2 mb-10">
          {tabFilters.map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 ${
                activeTab === tab
                  ? 'bg-[#5a8f5a] text-white shadow-lg shadow-[#5a8f5a]/25'
                  : 'glass text-[#5a7a5a] hover:bg-[#e8f5e8] border border-[#c8e6c8]'
              }`}
            >
              {tab}
              {tab !== 'Semua' && (
                <span className="ml-2 text-xs opacity-70">
                  ({newsItems.filter(n => n.source_type.toLowerCase() === tab.toLowerCase()).length})
                </span>
              )}
            </button>
          ))}
        </div>

        {/* News Grid */}
        {loading ? (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {[1, 2, 3].map((i) => (
              <div key={i} className="glass rounded-3xl overflow-hidden border border-[#c8e6c8]/50 animate-pulse">
                <div className="h-48 bg-[#e8f5e8]" />
                <div className="p-6 space-y-3">
                  <div className="h-4 bg-[#e8f5e8] rounded w-1/3" />
                  <div className="h-6 bg-[#e8f5e8] rounded w-full" />
                  <div className="h-4 bg-[#e8f5e8] rounded w-2/3" />
                </div>
              </div>
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="text-center py-16 text-[#5a7a5a]">
            <p className="text-lg">Belum ada berita dari kategori ini.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {filtered.slice(0, 3).map((news) => {
              const imgUrl = news.image_filename
                ? `${BACKEND_URL}/public/assets/uploads/news/${news.image_filename}`
                : null
              const color = sourceColors[news.source_type] || sourceColors.pusat
              const pillColor = sourcePillColors[news.source_type] || sourcePillColors.pusat

              return (
                <article
                  key={news.id}
                  className="group relative glass rounded-3xl overflow-hidden border border-[#c8e6c8]/50 hover:border-[#5a8f5a]/30 transition-all duration-500 hover:shadow-2xl hover:shadow-[#5a8f5a]/10 hover:-translate-y-2"
                >
                  {/* Image / Gradient Header */}
                  <div className={`h-48 bg-gradient-to-br ${color} relative overflow-hidden`}>
                    {imgUrl ? (
                      <img
                        src={imgUrl}
                        alt={news.title}
                        className="w-full h-full object-cover opacity-80"
                        onError={(e) => {
                          (e.target as HTMLImageElement).style.display = 'none'
                        }}
                      />
                    ) : (
                      <>
                        {/* Decorative leaf patterns */}
                        <svg className="absolute top-0 right-0 w-32 h-32 text-white/10" viewBox="0 0 100 100">
                          <path d="M100 0 Q60 20, 50 60 Q40 90, 0 100 L100 100 Z" fill="currentColor" />
                        </svg>
                        <svg className="absolute bottom-0 left-0 w-24 h-24 text-white/10" viewBox="0 0 100 100">
                          <ellipse cx="50" cy="50" rx="45" ry="40" fill="currentColor" />
                        </svg>
                      </>
                    )}
                    
                    {/* Category badge */}
                    <div className="absolute top-4 left-4">
                      <span className={`px-3 py-1.5 rounded-full ${pillColor} text-white text-xs font-semibold uppercase tracking-wide`}>
                        {sourceLabel(news)}
                      </span>
                    </div>
                  </div>

                  {/* Content */}
                  <div className="p-6">
                    {/* Meta */}
                    <div className="flex items-center gap-4 text-sm text-[#6a8a6a] mb-4">
                      <div className="flex items-center gap-1.5">
                        <Calendar className="w-4 h-4" />
                        {formatDate(news.published_at)}
                      </div>
                      {news.bpdas_name && (
                        <div className="flex items-center gap-1.5">
                          <MapPin className="w-4 h-4" />
                          {news.bpdas_name}
                        </div>
                      )}
                    </div>

                    {/* Title */}
                    <h3 className="font-serif text-xl font-semibold text-[#2d4a2d] mb-3 group-hover:text-[#3d5a3d] transition-colors line-clamp-2">
                      {news.title}
                    </h3>

                    {/* Excerpt */}
                    <p className="text-[#5a7a5a] leading-relaxed line-clamp-2 mb-4">
                      {truncate(stripHtml(news.content))}
                    </p>

                    {/* Read more */}
                    <Link
                      href={`${BACKEND_URL}/public/kabar-kehutanan`}
                      className="flex items-center gap-2 text-[#5a8f5a] font-medium text-sm group-hover:gap-3 transition-all"
                    >
                      Baca Selengkapnya
                      <ArrowRight className="w-4 h-4" />
                    </Link>
                  </div>
                </article>
              )
            })}
          </div>
        )}
      </div>
    </section>
  )
}
