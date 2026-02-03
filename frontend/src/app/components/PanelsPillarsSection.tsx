import React from 'react';
import { Users, DollarSign, UserCheck, MessageCircle, FileText, Camera, Video, PenTool, Mic } from 'lucide-react';

const panelsPillarsData = [
  { icon: <Users />, name: 'Event Panel', description: 'Manage event planning and execution', members: 28 },
  { icon: <DollarSign />, name: 'Financial Panel', description: 'Manage budgets and financial planning', members: 15 },
  { icon: <UserCheck />, name: 'HR Panel', description: 'Manage recruitment and member relations', members: 12 },
  { icon: <MessageCircle />, name: 'PR Panel', description: 'Manage public relations and media outreach', members: 10 },
  { icon: <FileText />, name: 'Secretarial Panel', description: 'Manage documentation and administrative tasks', members: 7 },
  { icon: <Camera />, name: 'Photography Pillar', description: 'Capture stunning visuals and moments', members: 37 },
  { icon: <Video />, name: 'Videography Pillar', description: 'Create compelling video content', members: 26 },
  { icon: <PenTool />, name: 'Creative Design Pillar', description: 'Visual identity and graphic design', members: 22 },
  { icon: <PenTool />, name: 'Editorial Pillar', description: 'Content creation and editorial processes', members: 16 },
  { icon: <Mic />, name: 'Announcing Pillar', description: 'Manage event hosting and public speaking initiatives', members: 94 },
];

const PanelsPillarsSection: React.FC = () => {
  return (
    <section className="py-16 bg-gradient-to-b from-gray-900 to-gray-800 text-white">
      <div className="container mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-8">Our Panels & Pillars</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {panelsPillarsData.map((item, index) => (
            <div key={index} className="bg-gray-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
              <div className="text-blue-400 mb-4">{item.icon}</div>
              <h3 className="text-xl font-semibold mb-2">{item.name}</h3>
              <p className="text-gray-300 mb-4">{item.description}</p>
              <span className="inline-block bg-blue-500 text-white text-sm font-medium px-3 py-1 rounded-full">
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