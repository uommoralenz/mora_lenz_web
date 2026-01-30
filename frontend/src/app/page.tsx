"use client";

import { HeroSection } from "@/app/components/HeroSection";
import { AboutSection } from "@/app/components/AboutSection";
import { PhotoGallery } from "@/app/components/PhotoGallery";
import { AdSection } from "@/app/components/AdSection";
import { Footer } from "@/app/components/Footer";

export default function Home() {
  return (
    <div className="min-h-screen bg-black">
      <HeroSection />
      <AboutSection />
      <PhotoGallery />
      <AdSection />
      <Footer />
    </div>
  );
}
