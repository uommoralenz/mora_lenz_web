/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,

  // Every request to the Laravel API is made server-side, so the browser never
  // holds the admin token and never talks to the university server directly.
  experimental: {
    serverActions: {
      // Image uploads go through server actions, so the default 1 MB body
      // limit is far too small.
      bodySizeLimit: "12mb",
    },
  },

  async headers() {
    return [
      {
        source: "/:path*",
        headers: [
          { key: "X-Frame-Options", value: "DENY" },
          { key: "X-Content-Type-Options", value: "nosniff" },
          { key: "Referrer-Policy", value: "no-referrer" },
          { key: "Strict-Transport-Security", value: "max-age=31536000; includeSubDomains" },
          { key: "Permissions-Policy", value: "camera=(), microphone=(), geolocation=(), payment=(), usb=()" },
          { key: "Cross-Origin-Opener-Policy", value: "same-origin" },
          // Next.js needs inline scripts, so script-src is not locked down; these
          // directives still block framing, plugins, <base> tricks and form hijacking.
          {
            key: "Content-Security-Policy",
            value: "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'",
          },
          // This is a private admin tool; keep it out of search results.
          { key: "X-Robots-Tag", value: "noindex, nofollow" },
        ],
      },
    ];
  },
};

export default nextConfig;
