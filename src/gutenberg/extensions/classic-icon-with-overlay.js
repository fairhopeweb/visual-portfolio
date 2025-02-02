/**
 * WordPress dependencies
 */
const { addFilter } = wp.hooks;

/**
 * Add overlay automatically for Classic style, when Display Icon option enabled.
 */
addFilter(
  'vpf.editor.controls-on-change',
  'vpf/editor/controls-on-change/classic-icon-with-overlay',
  (newAttributes, control, val, attributes) => {
    if (
      'items_style_default__show_icon' === control.name &&
      val &&
      !attributes.items_style_default__bg_color
    ) {
      newAttributes.items_style_default__bg_color = '#000';
      newAttributes.items_style_default__text_color = '#fff';
    }

    return newAttributes;
  }
);
