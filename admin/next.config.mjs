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
          // This is a private admin tool; keep it out of search results.
          { key: "X-Robots-Tag", value: "noindex, nofollow" },
        ],
      },
    ];
  },
};

export default nextConfig;
