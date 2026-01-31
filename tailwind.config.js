/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./app/Views/**/*.php"],
  theme: {
    extend: {
      colors: {
        background: "#020617", // slate-950
        surface: {
          DEFAULT: "#0f172a", // slate-900
          hover: "#1e293b", // slate-800
        },
        primary: {
          DEFAULT: "#4ade80", // green-400
          hover: "#22c55e", // green-500
          text: "#020617", // slate-950
        },
        secondary: {
          DEFAULT: "#334155", // slate-700
          hover: "#475569", // slate-600
          text: "#f8fafc", // slate-50
        },
        danger: {
          DEFAULT: "#ef4444", // red-500
          hover: "#dc2626", // red-600
        },
        text: {
          main: "#f8fafc", // slate-50
          muted: "#94a3b8", // slate-400
        },
        border: "#1e293b", // slate-800
      },
      fontFamily: {
        sans: ["Outfit", "sans-serif"],
      },
      borderRadius: {
        sm: "8px",
        md: "16px",
        lg: "24px",
      },
      boxShadow: {
        glow: "0 0 15px rgba(74, 222, 128, 0.15)",
      },
    },
  },
  plugins: [],
};
