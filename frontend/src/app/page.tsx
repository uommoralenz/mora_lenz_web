"use client";

import { HeroSection } from "@/app/components/HeroSection";
import { AboutSection } from "@/app/components/AboutSection";
import { PhotoGallery } from "@/app/components/PhotoGallery";
import { EventsActivities } from "@/app/components/Events&Activities";
import { AdSection } from "@/app/components/AdSection";
import { Footer } from "@/app/components/Footer";
import { TeamSection } from "@/app/components/TeamSection";

export default function Home() {
  return (
    <div className="min-h-screen bg-black">
      <HeroSection />
      <AboutSection />
      <PhotoGallery />
      <EventsActivities />
      <AdSection />
      <Footer />
    </div>
  );
}
