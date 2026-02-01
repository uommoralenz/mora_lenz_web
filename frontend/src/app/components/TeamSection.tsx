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
              name="Dr. Thesara Jayawardena"
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
              position="Vice President"
              about="Supporting club leadership and managing day-to-day operations to ensure smooth functioning of all activities."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Secretary"
              about="Managing club documentation, communications, and ensuring proper record-keeping of all official matters."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Assistant Secretary"
              about="Supporting secretarial duties and helping maintain organized communication channels within the club."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Junior Treasurer"
              about="Managing club finances, budgets, and ensuring transparent financial operations for all activities."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Assistant Junior Treasurer"
              about="Supporting financial management and helping maintain accurate financial records and transactions."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Chief Coordinator"
              about="Orchestrating major events and ensuring seamless coordination between different club departments."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Chief Coordinator"
              about="Leading coordination efforts and managing cross-departmental collaboration for successful project execution."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Event Coordinator & Technical Lead"
              about="Combining event management expertise with technical skills to deliver outstanding multimedia experiences."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Event Coordinator"
              about="Planning and executing engaging events that showcase the creative talents of our club members."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Photography Pillar Head"
              about="Leading the photography team with expertise in capturing stunning visuals and mentoring emerging photographers."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Assistant Photography Pillar Head"
              about="Supporting photography initiatives and helping develop technical skills within the photography department."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Creative Design Pillar Head"
              about="Directing creative design projects and maintaining the visual identity of Mora Lenz across all platforms."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Videography Pillar Head"
              about="Leading video production initiatives and creating compelling visual narratives for club documentation."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Assistant Videography Pillar Head"
              about="Supporting video production and helping coordinate filming activities for various club events."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Editorial Pillar Head"
              about="Overseeing content creation and editorial processes to maintain high-quality club publications."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Assistant Editorial Pillar Head"
              about="Supporting editorial work and helping curate engaging content for club communications."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Announcing Pillar Head"
              about="Leading event hosting and public speaking initiatives, bringing energy and professionalism to club events."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
          <div>
            <PositionCard
              name="Osada Bimsara"
              position="Assistant Announcing Pillar Head"
              about="Supporting announcement activities and helping develop presentation skills within the club."
              imageUrl={placeholder}
              order={["photo", "name", "position", "about"]}
            />
          </div>
        </section>
      </div>
    </section>
  );
}