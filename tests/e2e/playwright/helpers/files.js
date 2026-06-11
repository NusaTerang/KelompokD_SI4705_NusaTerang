import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';

const tinyPngBase64 =
  'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';

export function createDummyImage(filename = 'nusa-terang-e2e.png') {
  const imagePath = path.join(os.tmpdir(), filename);
  fs.writeFileSync(imagePath, Buffer.from(tinyPngBase64, 'base64'));

  return imagePath;
}
