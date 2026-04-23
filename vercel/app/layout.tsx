import type { Metadata } from 'next'
import { Inter, Playfair_Display } from 'next/font/google'
import { Analytics } from '@vercel/analytics/next'
import './globals.css'

const inter = Inter({ 
  subsets: ['latin'],
  variable: '--font-inter',
})

const playfair = Playfair_Display({ 
  subsets: ['latin'],
  variable: '--font-playfair',
})

export const metadata: Metadata = {
  title: 'BibitGratis.com — Platform Resmi Distribusi Bibit Tanaman Hutan Indonesia',
  description: 'Platform resmi Kementerian Lingkungan Hidup dan Kehutanan RI untuk akses informasi dan distribusi bibit tanaman hutan gratis bagi masyarakat. Pantau stok, ajukan permintaan, dan berkontribusi untuk penghijauan Indonesia.',
  keywords: ['bibit gratis', 'bibit tanaman hutan', 'penghijauan', 'BPDAS', 'KLHK', 'distribusi bibit', 'bibit gratis indonesia', 'ayo tanam pohon'],
  openGraph: {
    title: 'BibitGratis.com — Ayo Tanam Pohon',
    description: 'Dapatkan bibit tanaman hutan gratis dari persemaian resmi BPDAS/BPTH di seluruh Indonesia.',
    siteName: 'BibitGratis.com',
    locale: 'id_ID',
    type: 'website',
  },
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="id" className={`${inter.variable} ${playfair.variable} bg-background`}>
      <body className="font-sans antialiased">
        {children}
        {process.env.NODE_ENV === 'production' && <Analytics />}
      </body>
    </html>
  )
}
