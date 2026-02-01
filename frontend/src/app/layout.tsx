import type { Metadata } from "next";

import "./globals.css";



export const metadata: Metadata = {
  title: "Mora Lenz",
  description: "Mora Lenz Official Website",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body
        style={{
          fontFamily:
            "system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif",
        }}
      >
        {children}
      </body>
    </html>
  );
}
