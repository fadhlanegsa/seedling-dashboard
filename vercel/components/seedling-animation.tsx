'use client'

import { motion, AnimatePresence } from 'framer-motion'
import { useState, useEffect } from 'react'

export default function SeedlingAnimation() {
  const [phase, setPhase] = useState<'seedling' | 'growing' | 'tree'>('seedling')
  const [particles, setParticles] = useState<Array<{ id: number; x: number; y: number }>>([])

  useEffect(() => {
    const timer1 = setTimeout(() => {
      // Generate particles for transformation
      const newParticles = Array.from({ length: 12 }, (_, i) => ({
        id: i,
        x: (Math.random() - 0.5) * 120,
        y: (Math.random() - 0.5) * 80 - 40,
      }))
      setParticles(newParticles)
      setPhase('growing')
    }, 2000)

    const timer2 = setTimeout(() => {
      setPhase('tree')
    }, 3000)

    const timer3 = setTimeout(() => {
      setParticles([])
      setPhase('seedling')
    }, 9000)

    return () => {
      clearTimeout(timer1)
      clearTimeout(timer2)
      clearTimeout(timer3)
    }
  }, [phase === 'seedling' ? phase : null])

  return (
    <div className="relative flex items-center justify-center w-[400px] h-[420px]">
      {/* Ambient glow */}
      <motion.div 
        className="absolute w-64 h-64 bg-[#5a8f5a]/8 rounded-full blur-3xl"
        animate={{
          scale: phase === 'tree' ? 1.5 : 1,
          opacity: phase === 'tree' ? 0.15 : 0.08,
        }}
        transition={{ duration: 1.5, ease: 'easeOut' }}
      />

      <svg
        width="400"
        height="420"
        viewBox="0 0 400 420"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        className="relative z-10"
      >
        <defs>
          {/* Pot gradients */}
          <linearGradient id="potBody" x1="150" y1="320" x2="250" y2="390" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#f5ebe0" />
            <stop offset="50%" stopColor="#e8ddd0" />
            <stop offset="100%" stopColor="#d4c4b0" />
          </linearGradient>
          <linearGradient id="potRim" x1="140" y1="310" x2="260" y2="330" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#faf5ef" />
            <stop offset="100%" stopColor="#e8ddd0" />
          </linearGradient>
          
          {/* Stem gradient */}
          <linearGradient id="stemGreen" x1="200" y1="320" x2="200" y2="200" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#3d5a3d" />
            <stop offset="100%" stopColor="#5a8f5a" />
          </linearGradient>
          
          {/* Leaf gradients - Sage to Forest */}
          <linearGradient id="leafSage" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor="#7cb87c" />
            <stop offset="100%" stopColor="#5a8f5a" />
          </linearGradient>
          <linearGradient id="leafForest" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor="#5a8f5a" />
            <stop offset="100%" stopColor="#3d6b3d" />
          </linearGradient>
          <linearGradient id="leafDark" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor="#4a7a4a" />
            <stop offset="100%" stopColor="#2d4a2d" />
          </linearGradient>
          
          {/* Tree trunk gradient */}
          <linearGradient id="trunkGradient" x1="180" y1="400" x2="220" y2="250" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#5a4a3a" />
            <stop offset="50%" stopColor="#6b5a4a" />
            <stop offset="100%" stopColor="#7a6a5a" />
          </linearGradient>
          
          {/* Grass gradient */}
          <linearGradient id="grassGradient" x1="100" y1="380" x2="300" y2="400" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#7cb87c" />
            <stop offset="50%" stopColor="#5a8f5a" />
            <stop offset="100%" stopColor="#7cb87c" />
          </linearGradient>
        </defs>

        {/* === CERAMIC POT === */}
        <AnimatePresence>
          {phase !== 'tree' && (
            <motion.g
              initial={{ opacity: 1 }}
              exit={{ opacity: 0, y: 20 }}
              transition={{ duration: 0.8, ease: 'easeInOut' }}
            >
              {/* Pot shadow */}
              <ellipse cx="200" cy="392" rx="55" ry="10" fill="#3d5a3d" opacity="0.12" />
              
              {/* Pot body */}
              <path
                d="M150 330 L155 380 Q155 390 170 390 L230 390 Q245 390 245 380 L250 330 Q250 325 240 325 L160 325 Q150 325 150 330Z"
                fill="url(#potBody)"
                stroke="#d4c4b0"
                strokeWidth="0.5"
              />
              
              {/* Pot rim */}
              <path
                d="M145 325 Q145 315 162 315 L238 315 Q255 315 255 325 L255 330 Q255 338 238 338 L162 338 Q145 338 145 330 Z"
                fill="url(#potRim)"
                stroke="#d4c4b0"
                strokeWidth="0.5"
              />
              
              {/* Soil */}
              <ellipse cx="200" cy="328" rx="42" ry="6" fill="#5a4a3a" />
              <ellipse cx="200" cy="326" rx="38" ry="4" fill="#6b5a4a" />
            </motion.g>
          )}
        </AnimatePresence>

        {/* === GRASSY MOUND (appears when tree) === */}
        <AnimatePresence>
          {phase === 'tree' && (
            <motion.g
              initial={{ opacity: 0, scaleY: 0 }}
              animate={{ opacity: 1, scaleY: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.6, ease: 'easeOut' }}
              style={{ transformOrigin: '200px 395px' }}
            >
              {/* Ground shadow */}
              <ellipse cx="200" cy="398" rx="90" ry="12" fill="#3d5a3d" opacity="0.1" />
              
              {/* Grass mound base */}
              <ellipse cx="200" cy="390" rx="80" ry="14" fill="url(#grassGradient)" />
              
              {/* Grass blades */}
              {[...Array(9)].map((_, i) => (
                <motion.path
                  key={i}
                  d={`M${140 + i * 15} 385 Q${143 + i * 15} ${370 - (i % 3) * 5} ${145 + i * 15} 380`}
                  stroke="#5a8f5a"
                  strokeWidth="2"
                  strokeLinecap="round"
                  fill="none"
                  initial={{ pathLength: 0 }}
                  animate={{ pathLength: 1 }}
                  transition={{ delay: 0.3 + i * 0.05, duration: 0.4 }}
                />
              ))}
            </motion.g>
          )}
        </AnimatePresence>

        {/* === SEEDLING (initial state) === */}
        <AnimatePresence>
          {phase === 'seedling' && (
            <motion.g
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0, scale: 1.2 }}
              transition={{ duration: 0.5 }}
            >
              {/* Seedling stem */}
              <motion.path
                d="M200 326 Q200 300 200 270"
                stroke="url(#stemGreen)"
                strokeWidth="5"
                strokeLinecap="round"
                fill="none"
                initial={{ pathLength: 0 }}
                animate={{ pathLength: 1 }}
                transition={{ duration: 0.8, ease: 'easeOut' }}
              />
              
              {/* Left cotyledon leaf */}
              <motion.path
                d="M198 280 Q175 268 165 280 Q160 295 175 302 Q188 305 198 285"
                fill="url(#leafSage)"
                initial={{ scale: 0, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ delay: 0.6, duration: 0.5, ease: 'backOut' }}
                style={{ transformOrigin: '198px 285px' }}
              />
              
              {/* Right cotyledon leaf */}
              <motion.path
                d="M202 280 Q225 268 235 280 Q240 295 225 302 Q212 305 202 285"
                fill="url(#leafSage)"
                initial={{ scale: 0, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ delay: 0.7, duration: 0.5, ease: 'backOut' }}
                style={{ transformOrigin: '202px 285px' }}
              />
              
              {/* Top emerging leaf */}
              <motion.path
                d="M200 270 Q192 250 200 235 Q208 250 200 270"
                fill="url(#leafForest)"
                initial={{ scaleY: 0, opacity: 0 }}
                animate={{ scaleY: 1, opacity: 1 }}
                transition={{ delay: 0.9, duration: 0.6, ease: 'backOut' }}
                style={{ transformOrigin: '200px 270px' }}
              />
            </motion.g>
          )}
        </AnimatePresence>

        {/* === TRANSFORMATION PARTICLES === */}
        <AnimatePresence>
          {particles.length > 0 && (
            <g>
              {particles.map((particle) => (
                <motion.circle
                  key={particle.id}
                  cx={200}
                  cy={280}
                  r={3}
                  fill="#7cb87c"
                  initial={{ opacity: 0, x: 0, y: 0, scale: 0 }}
                  animate={{ 
                    opacity: [0, 1, 1, 0],
                    x: particle.x,
                    y: particle.y,
                    scale: [0, 1.2, 1, 0],
                  }}
                  transition={{ 
                    duration: 1.5,
                    ease: 'easeOut',
                    times: [0, 0.2, 0.7, 1],
                  }}
                />
              ))}
            </g>
          )}
        </AnimatePresence>

        {/* === MAJESTIC TREE === */}
        <AnimatePresence>
          {phase === 'tree' && (
            <motion.g
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0, scale: 0.8 }}
              transition={{ duration: 0.8 }}
            >
              {/* Tree trunk */}
              <motion.path
                d="M188 390 L185 340 Q183 320 190 300 L190 300 Q195 280 200 260 Q205 280 210 300 L210 300 Q217 320 215 340 L212 390 Z"
                fill="url(#trunkGradient)"
                initial={{ scaleY: 0 }}
                animate={{ scaleY: 1 }}
                transition={{ duration: 0.6, ease: 'easeOut' }}
                style={{ transformOrigin: '200px 390px' }}
              />
              
              {/* Trunk texture lines */}
              <motion.g
                initial={{ opacity: 0 }}
                animate={{ opacity: 0.3 }}
                transition={{ delay: 0.4 }}
              >
                <path d="M192 370 Q198 365 195 350" stroke="#4a3a2a" strokeWidth="0.5" fill="none" />
                <path d="M205 360 Q200 355 208 340" stroke="#4a3a2a" strokeWidth="0.5" fill="none" />
              </motion.g>

              {/* Tree canopy - layered for depth */}
              {/* Back layer (darker) */}
              <motion.path
                d="M200 80 
                   Q140 100 120 150 
                   Q100 200 130 240 
                   Q150 270 200 280 
                   Q250 270 270 240 
                   Q300 200 280 150 
                   Q260 100 200 80"
                fill="url(#leafDark)"
                initial={{ scale: 0, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ delay: 0.3, duration: 0.8, ease: 'backOut' }}
                style={{ transformOrigin: '200px 180px' }}
              />
              
              {/* Middle layer */}
              <motion.path
                d="M200 70 
                   Q150 90 135 135 
                   Q115 180 145 220 
                   Q165 250 200 260 
                   Q235 250 255 220 
                   Q285 180 265 135 
                   Q250 90 200 70"
                fill="url(#leafForest)"
                initial={{ scale: 0, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ delay: 0.5, duration: 0.7, ease: 'backOut' }}
                style={{ transformOrigin: '200px 165px' }}
              />
              
              {/* Front layer (lighter) */}
              <motion.path
                d="M200 65 
                   Q160 85 150 120 
                   Q135 160 160 195 
                   Q175 220 200 230 
                   Q225 220 240 195 
                   Q265 160 250 120 
                   Q240 85 200 65"
                fill="url(#leafSage)"
                initial={{ scale: 0, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ delay: 0.7, duration: 0.6, ease: 'backOut' }}
                style={{ transformOrigin: '200px 147px' }}
              />

              {/* Highlight accents on canopy */}
              <motion.g
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 1.2, duration: 0.5 }}
              >
                <ellipse cx="175" cy="130" rx="15" ry="20" fill="#8bc88b" opacity="0.4" />
                <ellipse cx="220" cy="150" rx="12" ry="15" fill="#8bc88b" opacity="0.3" />
                <ellipse cx="190" cy="180" rx="10" ry="12" fill="#9cd89c" opacity="0.25" />
              </motion.g>

              {/* Subtle branch hints */}
              <motion.g
                initial={{ opacity: 0 }}
                animate={{ opacity: 0.6 }}
                transition={{ delay: 1, duration: 0.4 }}
              >
                <path d="M200 260 Q170 240 155 210" stroke="#3d5a3d" strokeWidth="2" fill="none" strokeLinecap="round" />
                <path d="M200 260 Q230 240 245 210" stroke="#3d5a3d" strokeWidth="2" fill="none" strokeLinecap="round" />
                <path d="M200 260 Q185 220 175 180" stroke="#3d5a3d" strokeWidth="1.5" fill="none" strokeLinecap="round" />
                <path d="M200 260 Q215 220 225 180" stroke="#3d5a3d" strokeWidth="1.5" fill="none" strokeLinecap="round" />
              </motion.g>
            </motion.g>
          )}
        </AnimatePresence>

        {/* Floating nature particles (always visible but subtle) */}
        <motion.g
          animate={{ opacity: phase === 'tree' ? 1 : 0.5 }}
          transition={{ duration: 0.5 }}
        >
          {[
            { cx: 100, cy: 120, delay: 0 },
            { cx: 300, cy: 100, delay: 0.5 },
            { cx: 80, cy: 200, delay: 1 },
            { cx: 320, cy: 180, delay: 1.5 },
            { cx: 120, cy: 280, delay: 2 },
            { cx: 280, cy: 260, delay: 2.5 },
          ].map((p, i) => (
            <motion.circle
              key={i}
              cx={p.cx}
              cy={p.cy}
              r={2}
              fill="#5a8f5a"
              animate={{
                y: [0, -20, 0],
                x: [0, 8, 0],
                opacity: [0.3, 0.7, 0.3],
              }}
              transition={{
                duration: 4,
                delay: p.delay,
                repeat: Infinity,
                ease: 'easeInOut',
              }}
            />
          ))}
        </motion.g>
      </svg>
    </div>
  )
}
