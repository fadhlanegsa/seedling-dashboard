'use client'

import { useState, useEffect } from 'react'
import Link from 'next/link'
import { Menu, X, Leaf } from 'lucide-react'
import { Button } from '@/components/ui/button'

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'https://bibitgratis.com'

const navItems = [
  { label: 'Beranda', href: '/' },
  { label: 'Statistik', href: '#stats' },
  { label: 'Layanan', href: '#services' },
  { label: 'Kabar Kehutanan', href: '#news' },
  { label: 'Cari Bibit', href: `${BACKEND_URL}/public/stock-search` },
]

export default function Header() {
  const [isScrolled, setIsScrolled] = useState(false)
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50)
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  return (
    <header 
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled 
          ? 'glass shadow-sm' 
          : 'bg-transparent'
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3 group">
            <div className="relative">
              <div className="w-12 h-12 rounded-full bg-gradient-to-br from-[#5a8f5a] to-[#3d6b3d] flex items-center justify-center shadow-lg shadow-[#5a8f5a]/20 group-hover:shadow-[#5a8f5a]/40 transition-shadow">
                <Leaf className="w-6 h-6 text-white" />
              </div>
              <div className="absolute -bottom-1 -right-1 w-4 h-4 bg-[#90c990] rounded-full border-2 border-white" />
            </div>
            <div className="flex flex-col">
              <span className="font-serif text-xl font-semibold text-[#2d4a2d] tracking-tight">
                BibitGratis
              </span>
              <span className="text-xs text-[#5a8f5a] tracking-wider uppercase">
                Indonesia Hijau
              </span>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center gap-1">
            {navItems.map((item) => (
              <Link
                key={item.label}
                href={item.href}
                className="px-4 py-2 text-sm font-medium text-[#3d5a3d] hover:text-[#2d4a2d] relative group transition-colors"
              >
                {item.label}
                <span className="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-gradient-to-r from-[#5a8f5a] to-[#7cb87c] group-hover:w-3/4 transition-all duration-300 rounded-full" />
              </Link>
            ))}
          </nav>

          {/* CTA Buttons */}
          <div className="hidden lg:flex items-center gap-3">
            <Button 
              variant="ghost" 
              className="text-[#3d5a3d] hover:text-[#2d4a2d] hover:bg-[#e8f5e8]"
              asChild
            >
              <Link href={`${BACKEND_URL}/auth/login`}>Masuk</Link>
            </Button>
            <Button 
              className="bg-gradient-to-r from-[#5a8f5a] to-[#4a7f4a] hover:from-[#4a7f4a] hover:to-[#3d6b3d] text-white shadow-lg shadow-[#5a8f5a]/25 hover:shadow-[#5a8f5a]/40 transition-all"
              asChild
            >
              <Link href={`${BACKEND_URL}/public/request-form`}>Ajukan Permintaan</Link>
            </Button>
          </div>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            className="lg:hidden p-2 rounded-lg hover:bg-[#e8f5e8] transition-colors"
            aria-label="Toggle menu"
          >
            {isMobileMenuOpen ? (
              <X className="w-6 h-6 text-[#3d5a3d]" />
            ) : (
              <Menu className="w-6 h-6 text-[#3d5a3d]" />
            )}
          </button>
        </div>
      </div>

      {/* Mobile Menu */}
      <div 
        className={`lg:hidden absolute top-full left-0 right-0 glass border-t border-[#d4e8d4] transition-all duration-300 ${
          isMobileMenuOpen ? 'opacity-100 visible' : 'opacity-0 invisible'
        }`}
      >
        <nav className="flex flex-col p-6 gap-2">
          {navItems.map((item) => (
            <Link
              key={item.label}
              href={item.href}
              onClick={() => setIsMobileMenuOpen(false)}
              className="px-4 py-3 text-[#3d5a3d] hover:text-[#2d4a2d] hover:bg-[#e8f5e8] rounded-lg transition-colors"
            >
              {item.label}
            </Link>
          ))}
          <div className="flex flex-col gap-2 mt-4 pt-4 border-t border-[#d4e8d4]">
            <Button variant="outline" className="w-full border-[#5a8f5a] text-[#5a8f5a]" asChild>
              <Link href={`${BACKEND_URL}/auth/login`}>Masuk</Link>
            </Button>
            <Button className="w-full bg-gradient-to-r from-[#5a8f5a] to-[#4a7f4a] text-white" asChild>
              <Link href={`${BACKEND_URL}/public/request-form`}>Ajukan Permintaan</Link>
            </Button>
          </div>
        </nav>
      </div>
    </header>
  )
}
