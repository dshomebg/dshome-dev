/**
 * Транслитерация от кирилица на латиница
 */
const cyrillicToLatin: Record<string, string> = {
  а: "a", б: "b", в: "v", г: "g", д: "d", е: "e", ж: "zh", з: "z",
  и: "i", й: "y", к: "k", л: "l", м: "m", н: "n", о: "o", п: "p",
  р: "r", с: "s", т: "t", у: "u", ф: "f", х: "h", ц: "ts", ч: "ch",
  ш: "sh", щ: "sht", ъ: "a", ь: "y", ю: "yu", я: "ya",
  А: "A", Б: "B", В: "V", Г: "G", Д: "D", Е: "E", Ж: "Zh", З: "Z",
  И: "I", Й: "Y", К: "K", Л: "L", М: "M", Н: "N", О: "O", П: "P",
  Р: "R", С: "S", Т: "T", У: "U", Ф: "F", Х: "H", Ц: "Ts", Ч: "Ch",
  Ш: "Sh", Щ: "Sht", Ъ: "A", Ь: "Y", Ю: "Yu", Я: "Ya",
};

/**
 * Създава URL-friendly slug от текст
 * Поддържа кирилица, латиница и числа
 */
export function slugify(text: string): string {
  return text
    .split("")
    .map((char) => cyrillicToLatin[char] || char)
    .join("")
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, "") // Премахва специални символи
    .replace(/\s+/g, "-") // Заменя spaces с тире
    .replace(/-+/g, "-") // Премахва multiple тирета
    .replace(/^-+/, "") // Премахва тирета от началото
    .replace(/-+$/, ""); // Премахва тирета от края
}
