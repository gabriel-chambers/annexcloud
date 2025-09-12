const fs = require("fs")
const path = require("path")
const glob = require("glob")
​
const createWPMLXml = async () => {
  try {
    // Get block wpml config files
    const filenames = glob.sync("**/wpml-config.xml", {
      cwd: path.resolve(process.cwd(), "src/block/"),
    })
​
    // Get berg block wpml config data
    let guterBergBlockConfigXml = ""
    for (const file of filenames) {
      guterBergBlockConfigXml += await fs.promises.readFile(path.resolve(__dirname + "/src/block/", file), "utf-8")
      guterBergBlockConfigXml += "\n"
    }
​
    // Get custom fields and admin-texts wpml config data
    const customFieldConfigXml = await fs.promises.readFile(
      path.resolve(__dirname, "wpml-custom-field-config.xml"),
      "utf-8"
    )
​
    const wpmlContent = `<wpml-config>
      <gutenberg-blocks>${guterBergBlockConfigXml}</gutenberg-blocks>
      ${customFieldConfigXml}
    </wpml-config>`
​
    const wpmlConfigPath = path.resolve(__dirname, "wpml-config.xml")
​
    if (fs.existsSync(wpmlConfigPath)) {
      fs.promises.unlink(wpmlConfigPath)
      console.log(`Deleted file: ${wpmlConfigPath}`)
    }
    await fs.promises.writeFile(wpmlConfigPath, wpmlContent, "utf-8")
    console.log(`${wpmlConfigPath} was generated!`)
  } catch (err) {
    console.error(err)
  }
}
​
createWPMLXml()