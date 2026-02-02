"use client";

import React from "react";

type Block = "photo" | "about" | "name" | "position";
export type CardSize = "sm" | "md" | "lg";
export type PositionCardProps = {
  name: string;
  position: string;
  about: string;
  imageUrl: string;
  order?: Block[];
  size?: CardSize;
  className?: string;
};

export function PositionCard({
  name,
  position,
  about,
  imageUrl,
  order = ["photo", "about", "name", "position"],
  size = "md",
  className = "",
}: PositionCardProps) {
  const fallback = 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60';
  const photoSrc = imageUrl && imageUrl.trim().length > 0 ? imageUrl : fallback;

  const sizeStyles: Record<CardSize, { card: string; photo: string; name: string; position: string; about: string }> = {
    sm: {
      card: "w-full max-w-[280px] rounded-xl px-4 py-5 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-sky-500/20 hover:border-sky-400/50 cursor-pointer",
      photo: "h-20 w-20 transition-transform duration-300 group-hover:scale-110",
      name: "text-xl font-bold",
      position: "text-sm",
      about: "text-sm",
    },
    md: {
      card: "w-full max-w-[240px] rounded-xl px-4 py-5 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-sky-500/20 hover:border-sky-400/50 cursor-pointer",
      photo: "h-20 w-20 transition-transform duration-300 group-hover:scale-110",
      name: "text-base",
      position: "text-xs",
      about: "text-xs",
    },
    lg: {
      card: "w-full max-w-4xl rounded-2xl px-6 py-6 md:px-8",
      photo: "h-24 w-24 md:h-28 md:w-28",
      name: "text-xl md:text-2xl",
      position: "text-sm",
      about: "text-sm md:text-base",
    },
  };

  // Special horizontal/vertical responsive layout for large cards
  if (size === "lg") {
    return (
      <article
        className={`relative bg-slate-800/70 border border-white/10 shadow-lg overflow-hidden mx-auto 
          flex flex-col md:flex-row items-center gap-4 md:gap-6 
          ${sizeStyles[size].card} ${className}`}
        style={{ backdropFilter: "blur(10px)", WebkitBackdropFilter: "blur(10px)" }}
      >
        {/* Photo - centered on mobile, left on desktop */}
        <div className={`flex-shrink-0 ${sizeStyles[size].photo} rounded-full p-[3px] bg-gradient-to-b from-blue-500 to-indigo-500`}>
          <img
            src={photoSrc}
            alt={name}
            className="h-full w-full rounded-full object-cover bg-slate-900"
            loading="lazy"
            decoding="async"
          />
        </div>

        {/* Text - centered on mobile, left-aligned on desktop */}
        <div className="flex-1 text-center md:text-left">
          <p className={`text-slate-100/80 italic leading-relaxed ${sizeStyles[size].about}`}>
            "{about}"
          </p>
          <h3 className={`mt-4 font-bold text-sky-400 ${sizeStyles[size].name}`}>
            {name}
          </h3>
          <p className={`mt-1 text-slate-300 ${sizeStyles[size].position}`}>
            {position}
          </p>
        </div>
      </article>
    );
  }

  // Vertical layout for sm and md cards
  const blocks: Record<Block, React.ReactNode> = {
    photo: (
      <div key="photo" className={`mx-auto mb-3 ${sizeStyles[size].photo} rounded-full p-[2px] bg-gradient-to-b from-blue-500 to-indigo-500`}>
        <img
          src={photoSrc}
          alt={name}
          className="h-full w-full rounded-full object-cover bg-slate-900"
          loading="lazy"
          decoding="async"
        />
      </div>
    ),
    about: (
      <p
        key="about"
        className={`mx-auto text-slate-100/80 leading-relaxed text-center ${sizeStyles[size].about}`}
      >
        "{about}"
      </p>
    ),
    name: (
      <h3 key="name" className={`font-bold text-white text-center ${sizeStyles[size].name}`}>
        {name}
      </h3>
    ),
    position: (
      <div key="position" className="mt-1">
        <span className={`inline-block text-sky-400 font-medium text-center ${sizeStyles[size].position}`}>
          {position}
        </span>
      </div>
    ),
  };

  return (
    <article
      className={`group relative bg-slate-800/70 border border-white/10 shadow-lg overflow-hidden text-center ${sizeStyles[size].card} ${className}`}
      style={{ backdropFilter: "blur(10px)", WebkitBackdropFilter: "blur(10px)" }}
    >
      <div className="relative">
        {order.map((b) => blocks[b])}
      </div>
    </article>
  );
}