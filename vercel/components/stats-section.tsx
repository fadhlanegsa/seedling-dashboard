'use client'

import { useEffect, useRef, useState } from 'react'
import { TreeDeciduous, Truck, FileText, Building2, MapPin, Warehouse } from 'lucide-react'

// Shape of a single stat from the API
interface ApiStat {
  total_stock: number
  total_distributed: number
  total_requests: number
  total_bpdas: number
  total_provinces: number
  total_nurseries: number
}

interface StatItem {
  icon: React.ElementType
  value: number
  suffix: string
  label: string
  description: string
}

// Fallback data (shown while loading or on error)
const fallbackStats: ApiStat = {
  total_stock: 0,
  total_distributed: 0,
  total_requests: 0,
  total_bpdas: 0,
  total_provinces: 34,
  total_nurseries: 0,
}

function buildStatItems(data: ApiStat): StatItem[] {
  return [
    {
      icon: TreeDeciduous,
      value: data.total_stock,
      suffix: '',
      label: 'Total Stok Bibit',
      description: 'Bibit tersedia di seluruh Indonesia',
    },
    {
      icon: Truck,
      value: data.total_distributed,
      suffix: '',
      label: 'Bibit Terdistribusi',
      description: 'Berhasil disalurkan ke masyarakat',
    },
    {
      icon: FileText,
      value: data.total_requests,
      suffix: '',
      label: 'Permintaan Masuk',
      description: 'Permintaan dari masyarakat',
    },
    {
      icon: Building2,
      value: data.total_bpdas,
      suffix: '',
      label: 'Unit BPDAS',
      description: 'Balai pengelolaan aktif',
    },
    {
      icon: MapPin,
      value: data.total_provinces,
      suffix: '',
      label: 'Cakupan Provinsi',
      description: 'Jangkauan nasional',
    },
    {
      icon: Warehouse,
      value: data.total_nurseries,
      suffix: '',
      label: 'Persemaian Aktif',
      description: 'Lokasi persemaian produktif',
    },
  ]
}

function formatNumber(num: number): string {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(0) + 'K'
  }
  return num.toString()
}

interface CounterProps {
  value: number
  suffix: string
  isVisible: boolean
}

function AnimatedCounter({ value, suffix, isVisible }: CounterProps) {
  const [count, setCount] = useState(0)
  
  useEffect(() => {
    if (!isVisible) return
    
    const duration = 2000
    const steps = 60
    const increment = value / steps
    let current = 0
    
    const timer = setInterval(() => {
      current += increment
      if (current >= value) {
        setCount(value)
        clearInterval(timer)
      } else {
        setCount(Math.floor(current))
      }
    }, duration / steps)
    
    return () => clearInterval(timer)
  }, [isVisible, value])
  
  return (
    <span className="tabular-nums">
      {formatNumber(count)}{suffix}
    </span>
  )
}

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'https://bibitgratis.com'

export default function StatsSection() {
  const sectionRef = useRef<HTMLDivElement>(null)
  const [isVisible, setIsVisible] = useState(false)
  const [stats, setStats] = useState<StatItem[]>(buildStatItems(fallbackStats))
  const [loading, setLoading] = useState(true)
  
  // Fetch live stats from PHP API
  useEffect(() => {
    fetch(`${BACKEND_URL}/public/api-landing-data`)
      .then((res) => res.json())
      .then((result) => {
        if (result.success && result.data?.stats) {
          setStats(buildStatItems(result.data.stats))
        }
      })
      .catch(() => {
        // Silently fall back to zeros — will be visible as '0'
      })
      .finally(() => setLoading(false))
  }, [])

  // Intersection observer for scroll animation
  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true)
        }
      },
      { threshold: 0.2 }
    )
    
    if (sectionRef.current) {
      observer.observe(sectionRef.current)
    }
    
    return () => observer.disconnect()
  }, [])

  return (
    <section id="stats" ref={sectionRef} className="relative py-24 lg:py-32">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="inline-block text-sm font-medium text-[#5a8f5a] uppercase tracking-widest mb-4">
            Transparansi Data
          </span>
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-semibold text-[#2d4a2d] mb-6 leading-tight">
            Statistik Nasional
          </h2>
          <p className="text-lg text-[#5a7a5a] leading-relaxed">
            Data transparan distribusi bibit untuk Indonesia yang lebih hijau. 
            Diperbarui secara real-time dari seluruh unit persemaian.
          </p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
          {stats.map((stat, index) => (
            <div
              key={stat.label}
              className={`group bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-[#e0efe0] hover:border-[#5a8f5a]/30 transition-all duration-300 hover:shadow-md ${
                isVisible ? 'animate-count' : 'opacity-0'
              }`}
              style={{ animationDelay: `${index * 80}ms` }}
            >
              {/* Icon */}
              <div className="mb-4">
                <div className="w-12 h-12 rounded-xl bg-[#e8f5e8] flex items-center justify-center group-hover:bg-[#5a8f5a] transition-colors duration-300">
                  <stat.icon className="w-6 h-6 text-[#5a8f5a] group-hover:text-white transition-colors duration-300" />
                </div>
              </div>

              {/* Value */}
              <div className="mb-2">
                <span className="font-serif text-3xl lg:text-4xl font-semibold text-[#2d4a2d]">
                  {loading ? (
                    <span className="inline-block w-16 h-8 bg-[#e8f5e8] rounded animate-pulse" />
                  ) : (
                    <AnimatedCounter value={stat.value} suffix={stat.suffix} isVisible={isVisible} />
                  )}
                </span>
              </div>

              {/* Label */}
              <h3 className="text-base font-semibold text-[#3d5a3d] mb-1">
                {stat.label}
              </h3>

              {/* Description */}
              <p className="text-sm text-[#6a8a6a]">
                {stat.description}
              </p>
            </div>
          ))}
        </div>

        {/* Bottom decoration */}
        <div className="flex justify-center mt-16">
          <div className="flex items-center gap-3">
            <div className="w-12 h-[2px] bg-gradient-to-r from-transparent to-[#5a8f5a]/30" />
            <div className="w-2 h-2 rounded-full bg-[#5a8f5a]/50" />
            <div className="w-2 h-2 rounded-full bg-[#5a8f5a]" />
            <div className="w-2 h-2 rounded-full bg-[#5a8f5a]/50" />
            <div className="w-12 h-[2px] bg-gradient-to-l from-transparent to-[#5a8f5a]/30" />
          </div>
        </div>
      </div>
    </section>
  )
}
