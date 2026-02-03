import React from "react";
import {
  Users,
  DollarSign,
  UserCheck,
  MessageCircle,
  FileText,
  Camera,
  Video,
  PenTool,
  Mic,
} from "lucide-react";

const panelsPillarsData = [
  {
    icon: <Users />,
    name: "Event Panel",
    description: "Manage event planning and execution",
    members: 28,
  },
  {
    icon: <DollarSign />,
    name: "Financial Panel",
    description: "Manage budgets and financial planning",
    members: 15,
  },
  {
    icon: <UserCheck />,
    name: "HR Panel",
    description: "Manage recruitment and member relations",
    members: 12,
  },
  {
    icon: <MessageCircle />,
    name: "PR Panel",
    description: "Manage public relations and media outreach",
    members: 10,
  },
  {
    icon: <FileText />,
    name: "Secretarial Panel",
    description: "Manage documentation and administrative tasks",
    members: 7,
  },
  {
    icon: <Camera />,
    name: "Photography Pillar",
    description: "Capture stunning visuals and moments",
    members: 37,
  },
  {
    icon: <Video />,
    name: "Videography Pillar",
    description: "Create compelling video content",
    members: 26,
  },
  {
    icon: <PenTool />,
    name: "Creative Design Pillar",
    description: "Visual identity and graphic design",
    members: 22,
  },
  {
    icon: <PenTool />,
    name: "Editorial Pillar",
    description: "Content creation and editorial processes",
    members: 16,
  },
  {
    icon: <Mic />,
    name: "Announcing Pillar",
    description: "Manage event hosting and public speaking initiatives",
    members: 94,
  },
];

const PanelsPillarsSection: React.FC = () => {
  return (
    <section className="py-16 bg-gradient-to-b from-violet-950/50 via-indigo-950/40 to-violet-950/50 text-white">
      <div className="container mx-auto px-4 flex flex-col items-center">
        <h2 className="mt-14 mb-6 text-2xl md:text-3xl font-semibold tracking-wide text-gray-100 relative inline-block text-center">
          Our Panels & Pillars
          <span className="absolute left-1/2 -bottom-2 w-24 h-[2px] -translate-x-1/2 bg-gradient-to-r from-transparent via-sky-400 to-transparent"></span>
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {panelsPillarsData.map((item, index) => (
            <div
              key={index}
              className="bg-slate-900/60 backdrop-blur-sm p-6 rounded-lg border border-violet-500/20 hover:border-violet-400/40 shadow-md hover:shadow-lg transition-all"
            >
              <div className="text-violet-400 mb-4">{item.icon}</div>
              <h3 className="text-xl font-semibold mb-2">{item.name}</h3>
              <p className="text-gray-300 mb-4">{item.description}</p>
              <span className="inline-block bg-violet-600/80 text-white text-sm font-medium px-3 py-1 rounded-full">
                {item.members} Members
              </span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default PanelsPillarsSection;
