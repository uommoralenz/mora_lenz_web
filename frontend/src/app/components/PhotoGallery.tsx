"use client";

import { useEffect, useRef } from "react";
import Slider from "react-slick";
import { motion } from "framer-motion";

const galleryImages = [
  {
    url: "https://images.unsplash.com/photo-1758270703733-3663d99c9dd7?w=1200",
    title: "Photography Workshop",
    category: "Events",
  },
  {
    url: "https://images.unsplash.com/photo-1758901466295-a3a67cac9021?w=1200",
    title: "Film Production",
    category: "Projects",
  },
  {
    url: "https://images.unsplash.com/photo-1759784120360-8b5044b71f47?w=1200",
    title: "Studio Sessions",
    category: "Behind The Scenes",
  },
  {
    url: "https://images.unsplash.com/photo-1764254812010-78ab61ef114b?w=1200",
    title: "Campus Events",
    category: "Coverage",
  },
  {
    url: "https://images.unsplash.com/photo-1762356121454-877acbd554bb?w=1200",
    title: "Film Festival",
    category: "Events",
  },
  {
    url: "https://images.unsplash.com/photo-1735639013995-086e648eaa38?w=1200",
    title: "Team Collaboration",
    category: "Workshops",
  },
  {
    url: "https://images.unsplash.com/photo-1674668920910-85b8d3c187ca?w=1200",
    title: "Equipment Training",
    category: "Training",
  },
  {
    url: "https://images.unsplash.com/photo-1742497359858-8e0a442c9c55?w=1200",
    title: "Photo Exhibition",
    category: "Exhibitions",
  },
];

export function PhotoGallery() {
  const sliderRef = useRef<Slider>(null);

  const settings = {
    dots: true,
    infinite: true,
    speed: 800,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    pauseOnHover: true,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        },
      },
    ],
  };

  return (
    <section id="gallery" className="py-20 relative overflow-hidden bg-gradient-to-b from-indigo-950/60 via-slate-900 to-emerald-950/40">
      {/* Decorative elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-0 right-0 w-[400px] h-[400px] bg-teal-500/10 rounded-full blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-emerald-600/15 rounded-full blur-[120px]"></div>
      </div>
      <div className="container mx-auto px-6 relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
          className="text-center mb-16"
        >
          <h2 className="text-5xl md:text-6xl font-black text-white mb-4">
            Our <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">Gallery</span>
          </h2>
          <p className="text-xl text-gray-400">
            Explore our latest captures and creative work
          </p>
        </motion.div>

        <div className="max-w-7xl mx-auto">
          <Slider ref={sliderRef} {...settings}>
            {galleryImages.map((image, index) => (
              <div key={index} className="px-3">
                <motion.div
                  initial={{ opacity: 0, scale: 0.9 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className="relative group overflow-hidden rounded-2xl aspect-[4/3]"
                >
                  <img
                    src={image.url}
                    alt={image.title}
                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div className="absolute bottom-0 left-0 right-0 p-6">
                      <p className="text-sm text-purple-400 mb-2">{image.category}</p>
                      <h3 className="text-2xl font-bold text-white">{image.title}</h3>
                    </div>
                  </div>
                </motion.div>
              </div>
            ))}
          </Slider>
        </div>
      </div>
    </section>
  );
}
