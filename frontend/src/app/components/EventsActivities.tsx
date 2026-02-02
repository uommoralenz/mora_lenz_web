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

  // Sample data - replace with your actual data
  const upcomingEvents: Event[] = [
    {
      id: 1,
      name: "Art Exhibition",
      image: "/events/upcoming/Art exsi.jpg",
      date: new Date("2026-02-15T19:00:00"),
      description: "Join us for an evening of contemporary art showcasing local artists.",
      location: "Centra Court",
    },
    {
      id: 2,
      name: "Photography Workshop",
      image: "/events/upcoming/work shop.jpg",
      date: new Date("2026-03-20T10:00:00"),
      description: "A hands-on workshop for photography enthusiasts of all levels.",
      location: "Rubert Peiris Auditorium",
    },
  ];

  const pastEvents: Event[] = [
    {
      id: 3,
      name: "Medea Awards 2025",
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

  return (
    <section id="events" className="py-20 px-6 bg-black text-white">
      <div className="max-w-7xl mx-auto">
        {/* Upcoming Events */}
        <div className="mb-20">
          <h2 className="text-4xl md:text-5xl font-bold mb-12 text-center">
            Upcoming Events
          </h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {upcomingEvents.map((event) => (
              <div
                key={event.id}
                className="bg-zinc-900 rounded-lg overflow-hidden hover:transform hover:scale-105 transition-all duration-300"
              >
                <div className="relative h-48 w-full">
                  <Image
                    src={event.image}
                    alt={event.name}
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="p-4">
                  <h3 className="text-xl font-bold mb-2">{event.name}</h3>
                  {event.location && (
                    <p className="text-zinc-400 text-xs mb-2">
                      📍 {event.location}
                    </p>
                  )}
                  <div className="bg-zinc-800 rounded-lg p-3 mb-3">
                    <p className="text-xs text-zinc-400 mb-1">Countdown</p>
                    <p className="text-lg font-mono text-yellow-400">
                      {timeLeft[event.id] || "Calculating..."}
                    </p>
                  </div>
                  <p className="text-zinc-300 text-sm mb-3">{event.description}</p>
                  <p className="text-sm text-zinc-500">
                    {event.date.toLocaleDateString("en-US", {
                      weekday: "long",
                      year: "numeric",
                      month: "long",
                      day: "numeric",
                      hour: "2-digit",
                      minute: "2-digit",
                    })}
                  </p>
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
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {pastEvents.map((event) => (
              <div
                key={event.id}
                className="bg-zinc-900 rounded-lg overflow-hidden opacity-75 hover:opacity-100 transition-opacity duration-300"
              >
                <div className="relative h-48 w-full">
                  <Image
                    src={event.image}
                    alt={event.name}
                    fill
                    className="object-cover grayscale hover:grayscale-0 transition-all duration-300"
                  />
                </div>
                <div className="p-4">
                  <h3 className="text-xl font-bold mb-2">{event.name}</h3>
                  <p className="text-zinc-400 text-sm mb-2">
                    {event.description}
                  </p>
                  <p className="text-xs text-zinc-500">
                    {event.date.toLocaleDateString("en-US", {
                      month: "long",
                      day: "numeric",
                      year: "numeric",
                    })}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
