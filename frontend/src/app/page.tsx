"use client";

import { HeroSection } from "@/app/components/HeroSection";
import { AboutSection } from "@/app/components/AboutSection";
import { PhotoGallery } from "@/app/components/PhotoGallery";
import { EventsActivities } from "@/app/components/EventsActivities";
import { AdSection } from "@/app/components/AdSection";
import { Footer } from "@/app/components/Footer";
import { TeamSection } from "@/app/components/TeamSection";
import PanelsPillarsSection from "@/app/components/PanelsPillarsSection";
import ContactSection from "@/app/components/ContactSection";

export default function Home() {
  return (
    <div className="min-h-screen bg-gray-50 dark:bg-black transition-colors duration-300">
      <HeroSection />
      <AboutSection />
      <PhotoGallery />
      <EventsActivities />
      <TeamSection />
      <PanelsPillarsSection />
      <AdSection />
      <ContactSection />
      <Footer />
    </div>
  );
}
