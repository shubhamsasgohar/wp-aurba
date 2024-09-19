const { InspectorControls } = wp.blockEditor;
const createElement = wp.element.createElement;
const fragmentElement = wp.element.Fragment;
const { __ } = wp.i18n;
const { registerBlockType, getBlockTypes } = wp.blocks;
const { InnerBlocks, BlockEdit } = wp.editor;
const { PanelRow, PanelBody, SelectControl, RadioControl, TextControl } = wp.components;