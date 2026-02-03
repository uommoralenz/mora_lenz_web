"use client";

import React, { useState, useEffect } from "react";

type NavItem = {
  label: string;
  href?: string;
  children?: { label: string; href: string; description?: string; icon?: React.ReactNode }[];
};

const navItems: NavItem[] = [
  {
    label: "Home",
    href: "#home",
  },
  {
    label: "About",
    children: [
      { label: "Our Story", href: "#about", description: "Learn about Mora Lenz Media Club" },
      { label: "Our Team", href: "#team", description: "Meet our talented members" },
    ],
  },
  {
    label: "Events",
    href: "#events",
  },
  {
    label: "Gallery",
    href: "#gallery",
  },
  {
    label: "Contact",
    href: "#contact",
  },
];

export function Navbar() {
  const [isOpen, setIsOpen] = useState(false);
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const handleDropdownToggle = (label: string) => {
    setActiveDropdown(activeDropdown === label ? null : label);
  };

  // Smooth scroll handler
  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, href: string) => {
    e.preventDefault();
    const targetId = href.replace("#", "");
    const element = document.getElementById(targetId);
    
    if (element) {
      const navHeight = 80; // Account for fixed navbar height
      const elementPosition = element.getBoundingClientRect().top + window.scrollY;
      const offsetPosition = elementPosition - navHeight;

      window.scrollTo({
        top: offsetPosition,
        behavior: "smooth",
      });
    }

    // Close mobile menu and dropdown after clicking
    setIsOpen(false);
    setActiveDropdown(null);
  };

  return (
    <nav
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled
          ? "bg-slate-900/95 backdrop-blur-md shadow-lg shadow-black/10"
          : "bg-transparent"
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 md:h-20">
          {/* Logo */}
          <a 
            href="#home" 
            onClick={(e) => handleNavClick(e, "#home")}
            className="flex items-center gap-2 group cursor-pointer"
          >
            <div className="w-20 h-20 rounded-full overflow-hidden flex items-center justify-center transition-transform duration-300 group-hover:scale-110">
              <img src="/favicon.ico" alt="Mora Lenz Logo" className="w-full h-full object-cover"/>
            </div>
            <span className="text-white font-bold text-xl hidden sm:block">
              Mora Lenz
            </span>
          </a>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-1">
            {navItems.map((item) => (
              <div 
                key={item.label} 
                className="relative"
                onMouseEnter={() => item.children && setActiveDropdown(item.label)}
                onMouseLeave={() => item.children && setActiveDropdown(null)}
              >
                {item.children ? (
                  <button
                    onClick={() => handleDropdownToggle(item.label)}
                    className={`flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 ${
                      activeDropdown === item.label
                        ? "text-white bg-white/10"
                        : "text-slate-300 hover:text-white hover:bg-white/5"
                    }`}
                  >
                    {item.label}
                    <ChevronDownIcon
                      className={`w-4 h-4 transition-transform duration-200 ${
                        activeDropdown === item.label ? "rotate-180" : ""
                      }`}
                    />
                  </button>
                ) : (
                  <a
                    href={item.href || "#home"}
                    onClick={(e) => handleNavClick(e, item.href || "#home")}
                    className="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200 cursor-pointer"
                  >
                    {item.label}
                  </a>
                )}

                {/* Dropdown Menu */}
                {item.children && (
                  <div
                    className={`absolute top-full left-0 pt-2 w-64 transition-all duration-300 ${
                      activeDropdown === item.label
                        ? "opacity-100 translate-y-0 pointer-events-auto"
                        : "opacity-0 -translate-y-2 pointer-events-none"
                    }`}
                  >
                    <div className="bg-slate-800/95 backdrop-blur-md rounded-xl border border-white/10 shadow-xl shadow-black/20 overflow-hidden">
                      <div className="p-2">
                        {item.children.map((child) => (
                          <a
                            key={child.label}
                            href={child.href}
                            onClick={(e) => handleNavClick(e, child.href)}
                            className="block px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 group cursor-pointer"
                          >
                            <span className="text-white font-medium text-sm group-hover:text-emerald-400 transition-colors">
                              {child.label}
                            </span>
                            {child.description && (
                              <p className="text-slate-400 text-xs mt-0.5">
                                {child.description}
                              </p>
                            )}
                          </a>
                        ))}
                      </div>
                    </div>
                  </div>
                )}
              </div>
            ))}
          </div>

          {/* Desktop CTA Buttons */}
          <div className="hidden md:flex items-center gap-3">
            <a
              href="#contact"
              onClick={(e) => handleNavClick(e, "#contact")}
              className="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors duration-200 cursor-pointer"
            >
              Join Us
            </a>
            <a
              href="#events"
              onClick={(e) => handleNavClick(e, "#events")}
              className="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 flex items-center gap-2 cursor-pointer"
            >
              Explore Events
              <ArrowRightIcon className="w-4 h-4" />
            </a>
          </div>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsOpen(!isOpen)}
            className="md:hidden relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors duration-200"
            aria-label="Toggle menu"
          >
            <div className="w-6 h-5 relative flex flex-col justify-between">
              <span
                className={`w-full h-0.5 bg-white rounded-full transition-all duration-300 origin-center ${
                  isOpen ? "rotate-45 translate-y-2" : ""
                }`}
              />
              <span
                className={`w-full h-0.5 bg-white rounded-full transition-all duration-300 ${
                  isOpen ? "opacity-0 scale-0" : ""
                }`}
              />
              <span
                className={`w-full h-0.5 bg-white rounded-full transition-all duration-300 origin-center ${
                  isOpen ? "-rotate-45 -translate-y-2" : ""
                }`}
              />
            </div>
          </button>
        </div>
      </div>

      {/* Mobile Menu */}
      <div
        className={`md:hidden absolute top-full left-0 right-0 bg-slate-900/98 backdrop-blur-md border-t border-white/10 transition-all duration-300 ${
          isOpen
            ? "opacity-100 translate-y-0 pointer-events-auto"
            : "opacity-0 -translate-y-4 pointer-events-none"
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 py-4">
          {navItems.map((item) => (
            <div key={item.label} className="border-b border-white/5 last:border-0">
              {item.children ? (
                <div>
                  <button
                    onClick={() => handleDropdownToggle(item.label)}
                    className="w-full flex items-center justify-between px-4 py-3 text-white font-medium"
                  >
                    {item.label}
                    <ChevronDownIcon
                      className={`w-5 h-5 transition-transform duration-200 ${
                        activeDropdown === item.label ? "rotate-180" : ""
                      }`}
                    />
                  </button>
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      activeDropdown === item.label ? "max-h-96" : "max-h-0"
                    }`}
                  >
                    {item.children.map((child) => (
                      <a
                        key={child.label}
                        href={child.href}
                        onClick={(e) => handleNavClick(e, child.href)}
                        className="block px-8 py-2.5 text-slate-400 hover:text-emerald-400 transition-colors cursor-pointer"
                      >
                        {child.label}
                      </a>
                    ))}
                  </div>
                </div>
              ) : (
                <a
                  href={item.href || "#home"}
                  onClick={(e) => handleNavClick(e, item.href || "#home")}
                  className="block px-4 py-3 text-white font-medium hover:text-emerald-400 transition-colors cursor-pointer"
                >
                  {item.label}
                </a>
              )}
            </div>
          ))}

          {/* Mobile CTA */}
          <div className="mt-4 space-y-3">
            <a
              href="#events"
              onClick={(e) => handleNavClick(e, "#events")}
              className="block w-full px-5 py-3 text-center font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg cursor-pointer"
            >
              Explore Events
            </a>
            <a
              href="#contact"
              onClick={(e) => handleNavClick(e, "#contact")}
              className="block w-full px-5 py-3 text-center font-medium text-slate-300 border border-white/20 rounded-lg hover:bg-white/5 cursor-pointer"
            >
              Join Us
            </a>
          </div>
        </div>
      </div>
    </nav>
  );
}

// Icons
function ChevronDownIcon({ className }: { className?: string }) {
  return (
    <svg
      className={className}
      viewBox="0 0 16 16"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M4 6L8 10L12 6"
        stroke="currentColor"
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}

function ArrowRightIcon({ className }: { className?: string }) {
  return (
    <svg
      className={className}
      viewBox="0 0 16 16"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M3 8H13M13 8L9 4M13 8L9 12"
        stroke="currentColor"
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}
