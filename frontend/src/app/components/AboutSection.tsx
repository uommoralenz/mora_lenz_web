"use client";

import { motion } from "framer-motion";
import { Users, Award, Camera, Video } from "lucide-react";

const features = [
  {
    icon: Camera,
    title: "Photography",
    description: "Professional photography training and equipment access",
  },
  {
    icon: Video,
    title: "Videography",
    description: "Learn film production and video editing techniques",
  },
  {
    icon: Users,
    title: "Community",
    description: "Join a vibrant community of creative minds",
  },
  {
    icon: Award,
    title: "Events",
    description: "Participate in exhibitions and competitions",
  },
];

export function AboutSection() {
  return (
    <section className="py-20 bg-black relative overflow-hidden">
      {/* Background decoration */}
      <div className="absolute inset-0 opacity-10">
        <div className="absolute top-0 left-1/4 w-96 h-96 bg-purple-600 rounded-full blur-3xl"></div>
        <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-600 rounded-full blur-3xl"></div>
      </div>

      <div className="container mx-auto px-6 relative z-10">
        <div className="grid md:grid-cols-2 gap-16 items-center">
          <motion.div
            initial={{ opacity: 0, x: -50 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
          >
            <h2 className="text-5xl md:text-6xl font-black text-white mb-6">
              About <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">MoraLenz</span>
            </h2>
            <p className="text-lg text-gray-300 mb-6 leading-relaxed">
              MoraLenz is the premier media club at our university, dedicated to nurturing creativity and technical excellence in photography, videography, and digital media production.
            </p>
            <p className="text-lg text-gray-300 mb-8 leading-relaxed">
              Founded by passionate students, we've grown into a thriving community of visual storytellers, providing members with cutting-edge equipment, expert mentorship, and countless opportunities to showcase their talent.
            </p>
            <button className="px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-full hover:scale-105 transition-transform font-semibold">
              Learn More
            </button>
          </motion.div>

          <div className="grid grid-cols-2 gap-6">
            {features.map((feature, index) => {
              const Icon = feature.icon;
              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700 hover:border-purple-500 transition-colors"
                >
                  <Icon className="w-12 h-12 text-purple-400 mb-4" />
                  <h3 className="text-xl font-bold text-white mb-2">{feature.title}</h3>
                  <p className="text-gray-400 text-sm">{feature.description}</p>
                </motion.div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
}
