import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Mora Lenz Admin",
  description: "Admin dashboard for Mora Lenz Media Club",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
