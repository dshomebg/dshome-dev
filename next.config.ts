import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  webpack(config) {
    // SVG loader configuration for Tail Admin Pro icons
    config.module.rules.push({
      test: /\.svg$/,
      use: ["@svgr/webpack"],
    });

    return config;
  },
};

export default nextConfig;
