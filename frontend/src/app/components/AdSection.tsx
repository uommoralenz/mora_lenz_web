"use client";

import { motion } from "framer-motion";
import { Calendar, Trophy, BookOpen } from "lucide-react";

const ads = [
  {
    icon: Calendar,
    title: "Upcoming Workshop",
    subtitle: "Advanced Portrait Photography",
    date: "Feb 15, 2026",
    color: "from-purple-600 to-pink-600",
  },
  {
    icon: Trophy,
    title: "Annual Competition",
    subtitle: "Best Media Project 2026",
    date: "Deadline: Mar 1",
    color: "from-blue-600 to-cyan-600",
  },
  {
    icon: BookOpen,
    title: "New Course",
    subtitle: "Cinematic Video Editing",
    date: "Starting Soon",
    color: "from-green-600 to-teal-600",
  },
];

export function AdSection() {
  return (
    <section className="py-20 bg-gradient-to-b from-black to-gray-900">
      <div className="container mx-auto px-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
          className="text-center mb-16"
        >
          <h2 className="text-5xl md:text-6xl font-black text-white mb-4">
            Latest <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">Updates</span>
          </h2>
          <p className="text-xl text-gray-400">
            Don't miss out on exciting opportunities
          </p>
        </motion.div>

        <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
          {ads.map((ad, index) => {
            const Icon = ad.icon;
            return (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                viewport={{ once: true }}
                whileHover={{ scale: 1.05 }}
                className={`relative overflow-hidden rounded-2xl bg-gradient-to-br ${ad.color} p-8 cursor-pointer group`}
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                
                <div className="relative z-10">
                  <Icon className="w-16 h-16 text-white mb-4" />
                  <h3 className="text-2xl font-bold text-white mb-2">{ad.title}</h3>
                  <p className="text-white/90 text-lg mb-4">{ad.subtitle}</p>
                  <div className="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full">
                    <p className="text-sm text-white font-semibold">{ad.date}</p>
                  </div>
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
