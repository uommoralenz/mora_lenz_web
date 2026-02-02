"use client";

import { motion } from "framer-motion";
import { Camera, Film, Image as ImageIcon } from "lucide-react";

export function HeroSection() {
  return (
    <section id="home" className="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-purple-900 via-blue-900 to-black">
      {/* Animated background */}
      <div className="absolute inset-0 opacity-20">
        <div className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1758270703733-3663d99c9dd7?w=1920')] bg-cover bg-center"></div>
      </div>

      {/* Floating elements */}
      <motion.div
        className="absolute top-20 left-20"
        animate={{
          y: [0, -20, 0],
          rotate: [0, 5, 0],
        }}
        transition={{
          duration: 5,
          repeat: Infinity,
          ease: "easeInOut",
        }}
      >
        <Camera className="w-16 h-16 text-purple-400 opacity-30" />
      </motion.div>

      <motion.div
        className="absolute bottom-20 right-20"
        animate={{
          y: [0, 20, 0],
          rotate: [0, -5, 0],
        }}
        transition={{
          duration: 4,
          repeat: Infinity,
          ease: "easeInOut",
        }}
      >
        <Film className="w-16 h-16 text-blue-400 opacity-30" />
      </motion.div>

      {/* Main content */}
      <div className="relative z-10 text-center px-6 max-w-5xl">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-7xl md:text-9xl font-black text-white mb-6 tracking-tight">
            Mora<span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">Lenz</span>
          </h1>
          <p className="text-xl md:text-2xl text-gray-300 mb-8">
            Capturing Moments, Creating Stories
          </p>
          <p className="text-lg text-gray-400 mb-12 max-w-2xl mx-auto">
            Join the premier media club where creativity meets passion. From photography to videography, we bring your vision to life.
          </p>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.2 }}
          className="flex flex-wrap justify-center gap-4"
        >
          <button className="px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-full hover:scale-105 transition-transform font-semibold">
            Join Us Today
          </button>
          <button className="px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-full hover:bg-white/20 transition-colors font-semibold border border-white/20">
            View Our Work
          </button>
        </motion.div>
      </div>

      {/* Scroll indicator */}
      <motion.div
        className="absolute bottom-10 left-1/2 -translate-x-1/2"
        animate={{
          y: [0, 10, 0],
        }}
        transition={{
          duration: 2,
          repeat: Infinity,
          ease: "easeInOut",
        }}
      >
        <div className="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center pt-2">
          <div className="w-1 h-2 bg-white/50 rounded-full"></div>
        </div>
      </motion.div>
    </section>
  );
}
