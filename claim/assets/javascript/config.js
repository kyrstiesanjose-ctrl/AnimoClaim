/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./student/*.php", "./components/*.php"],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        // AnimoClaim Brand Colors
        "brand": {
          "50": "#f4fde4",
          "100": "#e8fbca",
          "200": "#d4f537",
          "300": "#c6f135",  // PRIMARY LIME
          "400": "#b0e61f",
          "500": "#8fd90e",
          "600": "#6dc00a",
          "700": "#54930a",
          "800": "#42720b",
          "900": "#385c0c",
        },
        "dark": {
          "bg": "#0f1419",      // Page background
          "card": "#1f2937",    // Card surface
          "border": "#2d3748",  // Subtle border
          "hover": "#374151",   // Hover state
          "text": "#ffffff",    // Primary text
          "muted": "#a1a1a1",   // Muted text
        },
        "status": {
          "active": "#10b981",
          "pending": "#f59e0b",
          "claimed": "#3b82f6",
          "expired": "#ef4444",
        }
      },
      fontFamily: {
        sans: ["Inter", "Montserrat", "system-ui", "sans-serif"],
        display: ["Montserrat", "sans-serif"],
      },
      fontSize: {
        "xs": ["12px", { lineHeight: "16px", fontWeight: "400" }],
        "sm": ["14px", { lineHeight: "20px", fontWeight: "400" }],
        "base": ["16px", { lineHeight: "24px", fontWeight: "400" }],
        "lg": ["18px", { lineHeight: "28px", fontWeight: "600" }],
        "xl": ["20px", { lineHeight: "30px", fontWeight: "600" }],
        "2xl": ["24px", { lineHeight: "32px", fontWeight: "700" }],
      },
      spacing: {
        "gutter": "16px",
        "gap-xs": "8px",
        "gap-sm": "12px",
        "gap-md": "16px",
        "gap-lg": "24px",
        "gap-xl": "32px",
      },
      borderRadius: {
        "btn": "8px",
        "card": "12px",
        "lg": "16px",
      },
      boxShadow: {
        "sm": "0 1px 2px 0 rgba(0, 0, 0, 0.05)",
        "md": "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
        "lg": "0 10px 15px -3px rgba(0, 0, 0, 0.2)",
      },
    },
  },
  plugins: [],
}