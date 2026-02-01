"use client";

import React from "react";

type Block = "photo" | "about" | "name" | "position";

export type PositionCardProps = {
  name: string;
  position: string;
  about: string;
  imageUrl: string;

  // control the order per-card
  order?: Block[];
};

export function PositionCard({
  name,
  position,
  about,
  imageUrl,
  order = ["photo", "about", "name", "position"], // default order
}: PositionCardProps) {
  const fallback = 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60';
  const photoSrc = imageUrl && imageUrl.trim().length > 0 ? imageUrl : fallback;

  const blocks: Record<Block, React.ReactNode> = {
    photo: (
      <div key="photo" className="mx-auto mb-6 h-24 w-24 rounded-full p-[3px] bg-gradient-to-b from-blue-500 to-indigo-500">
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
        className="mx-auto max-w-3xl text-slate-100/90 italic leading-relaxed text-base sm:text-lg"
      >
        "{about}"
      </p>
    ),
    name: (
      <h3 key="name" className="mt-6 text-xl sm:text-2xl font-semibold text-sky-300">
        {name}
      </h3>
    ),
    position: (
      <div key="position" className="mt-3">
        <span className="inline-block rounded-md px-3 py-1 text-sm sm:text-base font-semibold text-white">
          {position}
        </span>
      </div>
    ),
  };

  return (
    <article
      className="relative w-full max-w-4xl mx-auto rounded-[28px] bg-slate-800/70 border border-white/10 shadow-xl overflow-hidden"
      style={{ backdropFilter: "blur(10px)", WebkitBackdropFilter: "blur(10px)" }}
    >
      <div className="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/10 to-transparent" />

      <div className="relative px-6 sm:px-10 pt-10 pb-10 text-center">
        {order.map((b) => blocks[b])}
      </div>
    </article>
  );
}