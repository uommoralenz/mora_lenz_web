"use client";
import { PositionCard } from "./PositionCard";

export function TeamSection() {
  const placeholder = "https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60";
  return (
    <section className="py-20 bg-gradient-to-b from-emerald-900 to-teal-900">
      <div className="max-w-6xl mx-auto px-6">
        <p className="mb-3 text-xs uppercase tracking-wider text-gray-300">Our Team</p>
        <h2 className="text-4xl md:text-5xl font-extrabold text-white leading-tight">
          Meet the talented individuals who make Mora Lenz Media Club a thriving community of creative professionals.
        </h2>
        <p className="mt-4 text-gray-200 font-semibold">Advisors & Senior Leadership</p>

        <section className="mt-10 grid gap-8 sm:grid-cols-2">
          <div>
            <PositionCard
              name="Dr. Thesara Jayawarddena"
              position="Senior Treasurer & Advisor of Mora Lenz Media Club"
              about="I am deeply committed to nurturing the creative talents within Mora Lenz Media Club. Through strategic financial guidance and mentorship, I believe we can empower our members to achieve excellence in media arts while building a sustainable foundation for future generations."
              imageUrl="/Thesara-madam.webp"
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="President"
              about="Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="President"
              about="Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="President"
              about="Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="President"
              about="Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="President"
              about="Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
        </section>
      </div>
    </section>
  );
}