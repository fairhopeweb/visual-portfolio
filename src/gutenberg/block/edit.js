/**
 * External dependencies
 */
import classnames from 'classnames/dedupe';

/**
 * Internal dependencies
 */
import ControlsRender from '../components/controls-render';
import IframePreview from '../components/iframe-preview';

/**
 * WordPress dependencies
 */
const { Component, Fragment } = wp.element;

const { __ } = wp.i18n;

const { InspectorControls } = wp.blockEditor;

const { plugin_name: pluginName, controls_categories: registeredControlsCategories } =
  window.VPGutenbergVariables;

/**
 * Block Edit Class.
 */
export default class BlockEdit extends Component {
  componentDidMount() {
    const { attributes, setAttributes } = this.props;

    const {
      block_id: blockId,
      content_source: contentSource,
      setup_wizard: setupWizard,
    } = attributes;

    if (!setupWizard && (!blockId || !contentSource)) {
      setAttributes({
        setup_wizard: 'true',
      });
    }
  }

  componentDidUpdate() {
    const { attributes, setAttributes } = this.props;

    const { setup_wizard: setupWizard, content_source: contentSource, images } = attributes;

    // Set some starter attributes for different content sources.
    // And hide the setup wizard.
    if (setupWizard === 'true' && contentSource) {
      switch (contentSource) {
        case 'images':
          if (images && images.length) {
            setAttributes({
              setup_wizard: '',
              items_count: -1,
              items_click_action: 'popup_gallery',
            });
          }
          break;
        case 'post-based':
        case 'social-stream':
          setAttributes({
            setup_wizard: '',
            layout_elements: {
              top: {
                elements: [],
                align: 'center',
              },
              items: {
                elements: ['items'],
              },
              bottom: {
                elements: ['pagination'],
                align: 'center',
              },
            },
          });
          break;
        default:
          setAttributes({
            setup_wizard: '',
          });
          break;
      }
    }
  }

  // eslint-disable-next-line class-methods-use-this
  renderControls(props, isSetupWizard = false) {
    const { attributes } = props;

    let { content_source: contentSource } = attributes;

    // Saved layouts by default displaying Portfolio source.
    if (contentSource === 'portfolio') {
      contentSource = '';
    }

    return (
      <Fragment>
        <ControlsRender category="content-source" {...props} isSetupWizard={isSetupWizard} />
        {contentSource ? (
          <Fragment>
            {Object.keys(registeredControlsCategories).map((name) => {
              if (name === 'content-source') {
                return null;
              }

              return (
                <ControlsRender
                  key={name}
                  category={name}
                  {...props}
                  isSetupWizard={isSetupWizard}
                />
              );
            })}
          </Fragment>
        ) : (
          ''
        )}
      </Fragment>
    );
  }

  render() {
    const { attributes } = this.props;

    let { className } = this.props;

    const { setup_wizard: setupWizard, ghostkitClassname } = attributes;

    // add custom classname.
    if (ghostkitClassname) {
      className = classnames(className, ghostkitClassname);
    }

    return (
      <Fragment>
        {setupWizard !== 'true' ? (
          <InspectorControls>{this.renderControls(this.props)}</InspectorControls>
        ) : (
          ''
        )}
        <div className={className}>
          {setupWizard !== 'true' ? (
            <IframePreview {...this.props} />
          ) : (
            <div className="vpf-setup-wizard">
              <div className="vpf-setup-wizard-title">{pluginName}</div>
              <div className="vpf-setup-wizard-description">
                {__('Select content source for this layout', '@@text_domain')}
              </div>
              {this.renderControls(this.props, true)}
            </div>
          )}
        </div>
      </Fragment>
    );
  }
}
