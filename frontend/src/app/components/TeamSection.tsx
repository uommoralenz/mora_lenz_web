"use client";
import { PositionCard } from "./PositionCard";

type Block = "photo" | "about" | "name" | "position";

const placeholder =
  "https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60";

const teamMembers: {
  name: string;
  position: string;
  about: string;
  imageUrl: string;
  order?: Block[];
  size?: "sm" | "md" | "lg";
}[] = [
  {
    name: "Dr. Thesara Jayawardena",
    position: "Senior Treasurer & Advisor of Mora Lenz Media Club",
    about:
      "I am deeply committed to nurturing the creative talents within Mora Lenz Media Club. Through strategic financial guidance and mentorship, I believe we can empower our members to achieve excellence in media arts while building a sustainable foundation for future generations.",
    imageUrl: "/Thesara-madam.webp",
    order: ["photo", "about", "name", "position"],
    size: "lg",
  },
  {
    name: "Osada Bimsara",
    position: "President",
    about:
      "Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Vice President",
    about:
      "Supporting club leadership and managing day-to-day operations to ensure smooth functioning of all activities.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Secretary",
    about:
      "Managing club documentation, communications, and ensuring proper record-keeping of all official matters.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Assistant Secretary",
    about:
      "Supporting secretarial duties and helping maintain organized communication channels within the club.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Junior Treasurer",
    about:
      "Managing club finances, budgets, and ensuring transparent financial operations for all activities.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Assistant Junior Treasurer",
    about:
      "Supporting financial management and helping maintain accurate financial records and transactions.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Chief Coordinator",
    about:
      "Orchestrating major events and ensuring seamless coordination between different club departments.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Chief Coordinator",
    about:
      "Leading coordination efforts and managing cross-departmental collaboration for successful project execution.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Event Coordinator & Technical Lead",
    about:
      "Combining event management expertise with technical skills to deliver outstanding multimedia experiences.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Event Coordinator",
    about:
      "Planning and executing engaging events that showcase the creative talents of our club members.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Photography Pillar Head",
    about:
      "Leading the photography team with expertise in capturing stunning visuals and mentoring emerging photographers.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Assistant Photography Pillar Head",
    about:
      "Supporting photography initiatives and helping develop technical skills within the photography department.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Creative Design Pillar Head",
    about:
      "Directing creative design projects and maintaining the visual identity of Mora Lenz across all platforms.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Videography Pillar Head",
    about:
      "Leading video production initiatives and creating compelling visual narratives for club documentation.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Assistant Videography Pillar Head",
    about:
      "Supporting video production and helping coordinate filming activities for various club events.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Editorial Pillar Head",
    about:
      "Overseeing content creation and editorial processes to maintain high-quality club publications.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Assistant Editorial Pillar Head",
    about:
      "Supporting editorial work and helping curate engaging content for club communications.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Announcing Pillar Head",
    about:
      "Leading event hosting and public speaking initiatives, bringing energy and professionalism to club events.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
  {
    name: "Osada Bimsara",
    position: "Assistant Announcing Pillar Head",
    about:
      "Supporting announcement activities and helping develop presentation skills within the club.",
    imageUrl: placeholder,
    order: ["photo", "name", "position", "about"],
    size: "sm",
  },
];

export function TeamSection() {
  // Advisor section
  const advisor = teamMembers.slice(0, 1);

  // Executive Committee rows: 2, 2, then 4s
  const executiveRows = [
    { cols: 2, members: teamMembers.slice(1, 3) }, // President, Vice President
    { cols: 2, members: teamMembers.slice(3, 5) }, // Secretary, Assistant Secretary
    { cols: 4, members: teamMembers.slice(5, 9) }, // Treasurers & Coordinators
    { cols: 4, members: teamMembers.slice(9, 13) }, // Event Coordinators & Photography
    { cols: 4, members: teamMembers.slice(13, 17) }, // Design, Videography, Editorial
    { cols: 3, members: teamMembers.slice(17, 20) }, // Remaining members
  ];

  return (
    <section
      id="team"
      className="py-20 relative overflow-hidden bg-gradient-to-b from-blue-950/50 via-sky-950/40 to-violet-950/50"
    >
      {/* Decorative gradient orbs */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-0 left-1/3 w-[500px] h-[500px] bg-sky-500/15 rounded-full blur-[120px]"></div>
        <div className="absolute bottom-0 right-1/3 w-[400px] h-[400px] bg-violet-600/20 rounded-full blur-[100px]"></div>
        <div className="absolute top-1/2 left-0 w-[300px] h-[300px] bg-cyan-400/10 rounded-full blur-[80px]"></div>
      </div>
      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Header */}
        <h2 className="text-4xl md:text-5xl font-bold tracking-wide text-center">
          <span className="text-white">Our </span>
          <span className="bg-gradient-to-r from-sky-300 to-cyan-400 bg-clip-text text-transparent">
            Team
          </span>
        </h2>
        <p className="text-sm md:text-base text-center font-medium text-white/80 leading-relaxed max-w-3xl mx-auto">
          Meet the talented individuals who make Mora Lenz Media Club a thriving
          community of creative professionals.
        </p>

        {/* Advisors Section */}
        <div className="flex justify-center">
          <h3 className="mt-14 mb-6 text-2xl md:text-3xl font-semibold tracking-wide text-gray-100 relative inline-block">
            Advisors & Senior Leadership
            <span className="absolute left-1/2 -bottom-2 w-24 h-[2px] -translate-x-1/2 bg-gradient-to-r from-transparent via-sky-400 to-transparent"></span>
          </h3>
        </div>
        <div className="mt-6 justify-items-center">
          {advisor.map((member, idx) => (
            <PositionCard
              key={idx}
              name={member.name}
              position={member.position}
              about={member.about}
              imageUrl={member.imageUrl}
              order={member.order}
              size="lg"
            />
          ))}
        </div>

        {/* Executive Committee Section */}
        <div className="flex justify-center">
          <h3 className="mt-14 mb-6 text-2xl md:text-3xl font-semibold tracking-wide text-gray-100 relative inline-block">
            Executive Committee 2024/25
            <span className="absolute left-1/2 -bottom-2 w-24 h-[2px] -translate-x-1/2 bg-gradient-to-r from-transparent via-sky-400 to-transparent"></span>
          </h3>
        </div>

        <div className="mt-8 space-y-6">
          {executiveRows.map((row, rowIndex) => (
            <div
              key={rowIndex}
              className={[
                "grid gap-4 justify-items-center",
                row.cols === 2
                  ? "grid-cols-1 sm:grid-cols-2 max-w-2xl mx-auto"
                  : row.cols === 3
                    ? "grid-cols-1 sm:grid-cols-3 max-w-4xl mx-auto"
                    : "grid-cols-1 sm:grid-cols-2 lg:grid-cols-4",
              ].join(" ")}
            >
              {row.members.map((member, idx) => (
                <PositionCard
                  key={`${rowIndex}-${idx}`}
                  name={member.name}
                  position={member.position}
                  about={member.about}
                  imageUrl={member.imageUrl}
                  order={member.order}
                  size="sm"
                />
              ))}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
