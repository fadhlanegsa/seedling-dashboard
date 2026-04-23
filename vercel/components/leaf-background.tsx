'use client'

export default function LeafBackground() {
  return (
    <div className="fixed inset-0 pointer-events-none overflow-hidden z-0">
      {/* Soft gradient base */}
      <div className="absolute inset-0 bg-gradient-to-b from-[#fafcfa] via-white to-[#f8fbf8]" />
      
      {/* Subtle radial accents */}
      <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-[#5a8f5a]/[0.03] rounded-full blur-3xl" />
      <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-[#5a8f5a]/[0.02] rounded-full blur-3xl" />
      
      {/* Minimal corner leaves - top left */}
      <svg className="absolute -top-10 -left-10 w-48 h-48 text-[#5a8f5a] opacity-[0.06]" viewBox="0 0 200 200" aria-hidden="true">
        <path d="M0 0 Q60 30, 90 90 Q100 140, 70 180 Q35 200, 0 200 L0 0 Z" fill="currentColor" />
      </svg>
      
      {/* Minimal corner leaves - bottom right */}
      <svg className="absolute -bottom-10 -right-10 w-56 h-56 text-[#5a8f5a] opacity-[0.05] rotate-180" viewBox="0 0 200 200" aria-hidden="true">
        <path d="M0 0 Q60 30, 90 90 Q100 140, 70 180 Q35 200, 0 200 L0 0 Z" fill="currentColor" />
      </svg>
      
      {/* Floating leaf - top right */}
      <svg className="absolute top-32 right-20 w-16 h-20 text-[#5a8f5a] opacity-[0.08] animate-float-slow" viewBox="0 0 60 80" aria-hidden="true">
        <path d="M30 5 Q5 30, 12 55 Q18 75, 30 75 Q42 75, 48 55 Q55 30, 30 5 Z" fill="currentColor" />
        <path d="M30 15 L30 68" stroke="currentColor" strokeWidth="1" opacity="0.5" />
      </svg>
      
      {/* Floating leaf - left side */}
      <svg className="absolute top-1/2 left-8 w-12 h-16 text-[#5a8f5a] opacity-[0.06] animate-float-delay" viewBox="0 0 60 80" aria-hidden="true">
        <path d="M30 5 Q5 30, 12 55 Q18 75, 30 75 Q42 75, 48 55 Q55 30, 30 5 Z" fill="currentColor" />
      </svg>
      
      {/* Floating leaf - bottom left */}
      <svg className="absolute bottom-40 left-20 w-10 h-14 text-[#5a8f5a] opacity-[0.07] animate-float" viewBox="0 0 60 80" aria-hidden="true">
        <path d="M30 5 Q5 30, 12 55 Q18 75, 30 75 Q42 75, 48 55 Q55 30, 30 5 Z" fill="currentColor" />
      </svg>
    </div>
  )
}
