"use client";

import { useState, useEffect } from "react";
import Image from "next/image";

interface Event {
  id: number;
  name: string;
  image: string;
  date: Date;
  description: string;
  location?: string;
}

export function EventsActivities() {
  const [timeLeft, setTimeLeft] = useState<{ [key: number]: string }>({});
  const [currentPastEventIndex, setCurrentPastEventIndex] = useState(0);

  // Sample data - replace with your actual data
  const upcomingEvents: Event[] = [
    {
      id: 1,
      name: "Dummy event",
      image: "/events/upcoming/media-awards-2025.webp",
      date: new Date("2026-02-15T19:00:00"),
      description: "Join us for an evening of contemporary art showcasing local artists.",
      location: "Centra Court",
    },
    {
      id: 2,
      name: "Dummy thama itin mekath",
      image: "/events/upcoming/Sandwani-3.webp",
      date: new Date("2026-03-20T10:00:00"),
      description: "A hands-on workshop for photography enthusiasts of all levels.",
      location: "Rubert Peiris Auditorium",
    },
  ];

  const pastEvents: Event[] = [
    {
      id: 3,
      name: "Media Awards 2025",
      image: "/events/past/media-awards-2025.webp",
      date: new Date("2025-12-20T18:00:00"),
      description: "Sri Lanka's Biggest media competition of the year organized by Mora Lenz. Celebrating excellence in media and creativity.",
    },
    {
      id: 4,
      name: "Sandwani 3.0",
      image: "/events/past/Sandwani-3.webp",
      date: new Date("2025-10-10T14:00:00"),
      description: "A musical event organized by MoraLenz engaging with talented individuals in the field of music.",
    },
    {
      id: 5,
      name: "dummy event 1",
      image: "/events/past/media-awards-2025.webp",
      date: new Date("2025-12-20T18:00:00"),
      description: "The island has a documented history of over 3,000 years, with evidence of prehistoric human settlement dating back 125,000 years.",
    },
    {
      id: 6,
      name: "dummy event 2",
      image: "/events/past/Sandwani-3.webp",
      date: new Date("2025-12-20T18:00:00"),
      description: "explorers across the world as early as the Anuradhapura period. The Portuguese Empire established a colony in the sixteenth century, during a period of political",
    },
    {
      id: 7,
      name: "dummy event 3",
      image: "/events/past/media-awards-2025.webp",
      date: new Date("2025-12-20T18:00:00"),
      description: "explorers across the world as early as the Anuradhapura period. The Portuguese Empire established a colony in the sixteenth century, during a period of political",
    },
  ];

  useEffect(() => {
    const calculateTimeLeft = () => {
      const newTimeLeft: { [key: number]: string } = {};

      upcomingEvents.forEach((event) => {
        const difference = event.date.getTime() - new Date().getTime();

        if (difference > 0) {
          const days = Math.floor(difference / (1000 * 60 * 60 * 24));
          const hours = Math.floor((difference / (1000 * 60 * 60)) % 24);
          const minutes = Math.floor((difference / 1000 / 60) % 60);
          const seconds = Math.floor((difference / 1000) % 60);

          newTimeLeft[event.id] = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        } else {
          newTimeLeft[event.id] = "Event has started!";
        }
      });

      setTimeLeft(newTimeLeft);
    };

    calculateTimeLeft();
    const timer = setInterval(calculateTimeLeft, 1000);

    return () => clearInterval(timer);
  }, []);

  // Auto-rotate past events carousel
  const itemsPerPage = 3;
  const maxIndex = pastEvents.length - itemsPerPage;
  const [autoRotateKey, setAutoRotateKey] = useState(0);
  
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentPastEventIndex((prev) => {
        const nextIndex = prev + 1;
        // Loop back to 0 when reaching the end
        return nextIndex > maxIndex ? 0 : nextIndex;
      });
    }, 4000); // Change every 4 seconds

    return () => clearInterval(interval);
  }, [maxIndex, autoRotateKey]); // Reset interval when autoRotateKey changes

  const nextPastEvent = () => {
    setCurrentPastEventIndex((prev) => {
      const nextIndex = prev + 1;
      return nextIndex > maxIndex ? 0 : nextIndex;
    });
    setAutoRotateKey(prev => prev + 1); // Reset auto-rotate timer
  };

  const prevPastEvent = () => {
    setCurrentPastEventIndex((prev) => {
      const prevIndex = prev - 1;
      return prevIndex < 0 ? maxIndex : prevIndex;
    });
    setAutoRotateKey(prev => prev + 1); // Reset auto-rotate timer
  };

  const goToPastEvent = (index: number) => {
    setCurrentPastEventIndex(index);
    setAutoRotateKey(prev => prev + 1); // Reset auto-rotate timer
  };

  return (
    <section id="events" className="py-20 px-6 text-gray-900 dark:text-white relative overflow-hidden bg-gradient-to-b from-emerald-50/40 via-cyan-50/30 to-blue-50/50 dark:from-emerald-950/40 dark:via-cyan-950/30 dark:to-blue-950/50 transition-colors duration-300">
      {/* Animated background */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-20 left-10 w-[350px] h-[350px] bg-cyan-500/10 rounded-full blur-[80px] animate-pulse"></div>
        <div className="absolute bottom-20 right-10 w-[450px] h-[450px] bg-blue-600/15 rounded-full blur-[100px] animate-pulse" style={{animationDelay: '2s'}}></div>
        <div className="absolute top-1/2 right-1/4 w-[300px] h-[300px] bg-teal-400/10 rounded-full blur-[60px]"></div>
      </div>
      <div className="max-w-7xl mx-auto relative z-10">
        {/* Upcoming Events */}
        <div className="mb-20">
          <h2 className="text-4xl md:text-5xl font-bold mb-12 text-center">
            Upcoming Events
          </h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {upcomingEvents.map((event) => (
              <div
                key={event.id}
                className="relative rounded-lg overflow-hidden hover:transform hover:scale-105 transition-all duration-300 shadow-lg group cursor-pointer"
              >
                {/* Full Image Card */}
                <div className="relative h-96 w-full">
                  <Image
                    src={event.image}
                    alt={event.name}
                    fill
                    className="object-cover"
                  />
                  
                  {/* Countdown Badge - Top Right - Circular */}
                  <div className="absolute top-4 right-4 w-16 h-16 rounded-full bg-black/40 backdrop-blur-sm flex flex-col items-center justify-center">
                    <p className="text-yellow-400 text-2xl font-bold leading-none">
                      {timeLeft[event.id] ? 
                        `${Math.floor((event.date.getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24))}` 
                        : "0"}
                    </p>
                    <p className="text-white text-[9px] mt-0.5">DAYS</p>
                  </div>
                  
                  {/* Blurred bottom overlay */}
                  <div className="absolute bottom-0 left-0 right-0 h-32 backdrop-blur-sm bg-gradient-to-t from-black/50 to-transparent"></div>
                  
                  {/* Text content on image */}
                  <div className="absolute bottom-0 left-0 right-0 p-4">
                    <h3 className="text-xl font-bold mb-1 text-white drop-shadow-lg">{event.name}</h3>
                    {event.location && (
                      <p className="text-cyan-300 text-xs mb-1 drop-shadow-md">
                        📍 {event.location}
                      </p>
                    )}
                    <p className="text-zinc-200 text-xs mb-1 line-clamp-2 drop-shadow-md">{event.description}</p>
                    <p className="text-xs text-zinc-300 drop-shadow-md">
                      📅 {event.date.toLocaleDateString("en-US", {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                      })}
                    </p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Past Events */}
        <div>
          <h2 className="text-4xl md:text-5xl font-bold mb-12 text-center">
            Past Events
          </h2>
          
          {/* Carousel Container */}
          <div className="relative max-w-7xl mx-auto px-12">
            {/* Cards Container */}
            <div className="relative overflow-hidden">
              <div 
                className="flex transition-transform duration-500 ease-out"
                style={{ 
                  transform: `translateX(-${currentPastEventIndex * (100 / itemsPerPage)}%)`
                }}
              >
                {pastEvents.map((event) => (
                  <div
                    key={event.id}
                    className="flex-shrink-0 px-3"
                    style={{ width: `${100 / itemsPerPage}%` }}
                  >
                    <div className="relative rounded-lg overflow-hidden group cursor-pointer">
                      {/* Image with text overlay */}
                      <div className="relative h-[512px] w-full">
                        <Image
                          src={event.image}
                          alt={event.name}
                          fill
                          className="object-cover grayscale group-hover:grayscale-0 transition-all duration-300"
                        />
                        
                        {/* Blurred bottom overlay */}
                        <div className="absolute bottom-0 left-0 right-0 h-40 backdrop-blur-sm bg-gradient-to-t from-black/40 to-transparent"></div>
                        
                        {/* Text content on image */}
                        <div className="absolute bottom-0 left-0 right-0 p-6">
                          <h3 className="text-2xl font-bold mb-2 text-white drop-shadow-lg">{event.name}</h3>
                          <p className="text-zinc-200 text-sm mb-2 line-clamp-3 drop-shadow-md">
                            {event.description}
                          </p>
                          <p className="text-sm text-cyan-300 drop-shadow-md">
                            {event.date.toLocaleDateString("en-US", {
                              month: "long",
                              day: "numeric",
                              year: "numeric",
                            })}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Navigation Arrows */}
            <button
              onClick={prevPastEvent}
              className="absolute left-0 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 hover:scale-110 hover:-translate-x-1 text-white p-3 rounded-full transition-all duration-300 z-10 active:scale-95"
              aria-label="Previous events"
            >
              <svg className="w-6 h-6 transition-transform duration-300 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            
            <button
              onClick={nextPastEvent}
              className="absolute right-0 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 hover:scale-110 hover:translate-x-1 text-white p-3 rounded-full transition-all duration-300 z-10 active:scale-95"
              aria-label="Next events"
            >
              <svg className="w-6 h-6 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </button>

            {/* Dots Indicator */}
            <div className="flex justify-center gap-2 mt-8">
              {Array.from({ length: maxIndex + 1 }).map((_, index) => (
                <button
                  key={index}
                  onClick={() => goToPastEvent(index)}
                  className={`transition-all duration-300 rounded-full ${
                    index === currentPastEventIndex
                      ? 'bg-cyan-400 w-8 h-2'
                      : 'bg-zinc-600 hover:bg-zinc-500 w-2 h-2'
                  }`}
                  aria-label={`Go to position ${index + 1}`}
                />
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
