/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";
import icons from "./images/icons";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
//import './style.scss';

/**
 * Internal dependencies
 */
import Edit from "./edit";
import save from "./save";
// import attributes from json file
import attributes from "./inc/attributes.json";

export const name = "e25m/media-elements";

export const settings = {
  apiVersion: 2,
  title: __("Media Element", ""),
  description: __("Media Element", ""),
  icon: icons.blurb,
  category: "widgets",
  example: {},
  supports: {
    customClassName: false,
    html: false,
  },
  attributes,
  edit: Edit,
  save: save,
};
