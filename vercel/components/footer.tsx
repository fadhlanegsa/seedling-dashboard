'use client'

import Link from 'next/link'
import { Leaf, Mail, Phone, MapPin, Facebook, Twitter, Instagram, Youtube } from 'lucide-react'

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'https://bibitgratis.com'

const footerLinks = {
  layanan: [
    { label: 'Permintaan Bibit', href: `${BACKEND_URL}/public/request-form` },
    { label: 'Cari Stok Bibit', href: `${BACKEND_URL}/public/stock-search` },
    { label: 'Direktori Sumber Benih', href: `${BACKEND_URL}/public/seed-source-directory` },
    { label: 'Distribusi Bibit', href: `${BACKEND_URL}/public/distribution` },
  ],
  informasi: [
    { label: 'Kabar Kehutanan', href: `${BACKEND_URL}/public/kabar-kehutanan` },
    { label: 'Cara Pengajuan', href: `${BACKEND_URL}/public/howto` },
    { label: 'Tentang Program', href: `${BACKEND_URL}/public/howto` },
    { label: 'FAQ', href: `${BACKEND_URL}/public/howto` },
  ],
  akun: [
    { label: 'Daftar Akun', href: `${BACKEND_URL}/auth/register` },
    { label: 'Masuk', href: `${BACKEND_URL}/auth/login` },
    { label: 'Permintaan Saya', href: `${BACKEND_URL}/public/my-requests` },
    { label: 'Profil Saya', href: `${BACKEND_URL}/public/profile` },
  ],
}

const socialLinks = [
  { icon: Facebook, href: '#', label: 'Facebook' },
  { icon: Twitter, href: '#', label: 'Twitter' },
  { icon: Instagram, href: '#', label: 'Instagram' },
  { icon: Youtube, href: '#', label: 'Youtube' },
]

const currentYear = new Date().getFullYear()

export default function Footer() {
  return (
    <footer className="relative bg-[#2d4a2d] text-white overflow-hidden">
      {/* Decorative top wave */}
      <div className="absolute top-0 left-0 right-0 overflow-hidden">
        <svg className="relative block w-full h-16" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path d="M0 0 C300 100, 600 0, 900 100 L1200 0 L1200 120 L0 120 Z" fill="#f5faf5" />
        </svg>
      </div>

      {/* Decorative leaves */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none opacity-5">
        <svg className="absolute top-20 left-10 w-48 h-48" viewBox="0 0 100 100">
          <path d="M50 5 C20 25, 10 50, 15 75 C20 90, 35 95, 50 95 C65 95, 80 90, 85 75 C90 50, 80 25, 50 5 Z" fill="white" />
        </svg>
        <svg className="absolute bottom-20 right-10 w-64 h-64" viewBox="0 0 100 100">
          <ellipse cx="50" cy="50" rx="45" ry="40" fill="white" />
        </svg>
      </div>

      <div className="relative max-w-7xl mx-auto px-6 lg:px-8 pt-24 pb-12">
        {/* Main footer content */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-16">
          {/* Brand column */}
          <div className="lg:col-span-2">
            <Link href="/" className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
                <Leaf className="w-6 h-6 text-[#90c990]" />
              </div>
              <div>
                <span className="font-serif text-xl font-semibold block">BibitGratis</span>
                <span className="text-xs text-white/60 tracking-wider uppercase">Indonesia Hijau</span>
              </div>
            </Link>
            
            <p className="text-white/70 leading-relaxed mb-6 max-w-sm">
              Platform resmi Kementerian Lingkungan Hidup dan Kehutanan RI untuk distribusi 
              bibit tanaman hutan gratis bagi masyarakat Indonesia. Bersama membangun Indonesia yang hijau dan lestari.
            </p>

            {/* Contact info */}
            <div className="space-y-3">
              <div className="flex items-center gap-3 text-white/70">
                <Mail className="w-5 h-5 text-[#90c990]" />
                <span>info@bibitgratis.com</span>
              </div>
              <div className="flex items-center gap-3 text-white/70">
                <Phone className="w-5 h-5 text-[#90c990]" />
                <span>(021) 573-4618</span>
              </div>
              <div className="flex items-start gap-3 text-white/70">
                <MapPin className="w-5 h-5 text-[#90c990] flex-shrink-0 mt-0.5" />
                <span>Gedung Manggala Wanabakti, Blok I Lt. 1, Jakarta Pusat 10270</span>
              </div>
            </div>

            {/* Socials */}
            <div className="flex items-center gap-3 mt-6">
              {socialLinks.map((social) => (
                <Link
                  key={social.label}
                  href={social.href}
                  className="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#5a8f5a] transition-colors"
                  aria-label={social.label}
                >
                  <social.icon className="w-5 h-5" />
                </Link>
              ))}
            </div>
          </div>

          {/* Links columns */}
          <div>
            <h4 className="font-semibold mb-5 text-[#90c990]">Layanan</h4>
            <ul className="space-y-3">
              {footerLinks.layanan.map((link) => (
                <li key={link.label}>
                  <Link 
                    href={link.href} 
                    className="text-white/70 hover:text-white transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold mb-5 text-[#90c990]">Informasi</h4>
            <ul className="space-y-3">
              {footerLinks.informasi.map((link) => (
                <li key={link.label}>
                  <Link 
                    href={link.href} 
                    className="text-white/70 hover:text-white transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold mb-5 text-[#90c990]">Akun</h4>
            <ul className="space-y-3">
              {footerLinks.akun.map((link) => (
                <li key={link.label}>
                  <Link 
                    href={link.href} 
                    className="text-white/70 hover:text-white transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Bottom bar */}
        <div className="pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
          <p className="text-white/50 text-sm text-center md:text-left">
            © {currentYear} BibitGratis. Kementerian Lingkungan Hidup dan Kehutanan Republik Indonesia.
            Seluruh hak cipta dilindungi undang-undang.
          </p>
          <div className="flex gap-4 text-white/50 text-xs">
            <span>Kebijakan Privasi</span>
            <span>|</span>
            <span>Syarat & Ketentuan</span>
          </div>
        </div>
      </div>
    </footer>
  )
}
