const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const sourceSvg = path.join(__dirname, 'images/favicon.svg');
const outputDir = path.join(__dirname, 'images');

const icons = [
  { name: 'favicon-16x16.png', size: 16 },
  { name: 'favicon-32x32.png', size: 32 },
  { name: 'apple-touch-icon.png', size: 180 },
  { name: 'android-chrome-192x192.png', size: 192 },
  { name: 'android-chrome-512x512.png', size: 512 },
  { name: 'mstile-150x150.png', size: 150 },
  { name: 'mstile-70x70.png', size: 70 },
  { name: 'mstile-310x310.png', size: 310 },
  { name: 'mstile-310x150.png', size: 310, height: 150 },
  { name: 'og-image.png', size: 1200 },
];

async function generateIcons() {
  try {
    // Read the SVG file
    const svgBuffer = fs.readFileSync(sourceSvg);

    // Generate each icon size
    for (const icon of icons) {
      const outputPath = path.join(outputDir, icon.name);
      const height = icon.height || icon.size;

      await sharp(svgBuffer)
        .resize(icon.size, height, {
          fit: 'cover',
          background: { r: 255, g: 255, b: 255, alpha: 0 }
        })
        .png()
        .toFile(outputPath);

      console.log(`✓ Generated ${icon.name}`);
    }

    // Generate favicon.ico
    const icoPath = path.join(outputDir, 'favicon.ico');
    const faviconBuffer = await sharp(svgBuffer)
      .resize(32, 32, {
        fit: 'cover',
        background: { r: 255, g: 255, b: 255, alpha: 0 }
      })
      .png()
      .toBuffer();

    fs.writeFileSync(icoPath, faviconBuffer);
    console.log(`✓ Generated favicon.ico`);

    console.log('\n✓ All icons generated successfully!');
  } catch (error) {
    console.error('Error generating icons:', error);
    process.exit(1);
  }
}

generateIcons();
