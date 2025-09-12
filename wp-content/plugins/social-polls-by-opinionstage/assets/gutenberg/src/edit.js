import {__} from '@wordpress/i18n'
import {createBlock} from '@wordpress/blocks'

import './editor.scss'
// values for widgetType attribute:
import {
  WIDGET_POLL,
  WIDGET_PERSONALITY_QUIZ,
  WIDGET_TRIVIA_QUIZ,
  WIDGET_SURVEY,
} from './configuration.js'

export default function Edit({name, className, attributes, setAttributes, /*isSelected,*/ clientId}) {
  let {
    embedUrl,
    buttonText,
    insertItemImage,
    insertItemOsTitle,
    insertItemOsView,
    insertItemOsEdit,
    insertItemOsStatistics,
  } = attributes

  if ( ! OPINIONSTAGE_GUTENBERG_DATA.userLoggedIn ) {
    return (
      <div className={className}>
        <div className="os-widget-wrapper components-placeholder">
          <p className="components-heading">
            <img src={OPINIONSTAGE_GUTENBERG_DATA.brandLogoUrl} alt="Opinionstage Logg"/>
          </p>
          <p className="components-heading">Please connect WordPress to Opinion Stage to start adding polls, quizzes,
            surveys & forms
          </p>
          <a href={OPINIONSTAGE_GUTENBERG_DATA.loginPageUrl}
             className="opinionstage-button opinionstage-button__blue">Connect
          </a>
        </div>
      </div>
    )
  }

  const currentWidgetType = widgetTypeFromBlockName(name)
  const currentWidgetTitle = widgetTitleFromType(currentWidgetType)

  const placeWidget = function (widget) {
    const selectedWidgetType = backendWidgetTypeToBlockWidgetType(widget.type)
    const newAttributes = {
      widgetType: selectedWidgetType,
      lockEmbed: true,
      buttonText: 'Change',
      embedUrl: widget.landingPageUrl.replace(/^https?:\/\/[^/]+\//, '/'),
      insertItemImage: widget.imageUrl,
      insertItemOsTitle: widget.title,
      insertItemOsView: widget.landingPageUrl,
      insertItemOsEdit: widget.editUrl,
      insertItemOsStatistics: widget.statsUrl,
    }

    if (selectedWidgetType === currentWidgetType) {
      setAttributes(newAttributes)
    } else {
      // on widget type change we also want to change block,
      // in order to accommodate widget type for better UX.
      // https://wordpress.stackexchange.com/questions/305932/gutenberg-remove-add-blocks-with-custom-script
      const replacementBlock = createBlock(blockName(selectedWidgetType))
      replacementBlock.attributes = newAttributes

      wp.data.dispatch('core/block-editor').replaceBlock(
        clientId,
        replacementBlock
      )
    }
  }

  const selectWidget = e => {
    e.preventDefault()
    OpinionStage.contentPopup.open({
      preselectWidgetType: contentPopupWidgetType(), 
      onWidgetSelect: placeWidget
    })
  }

  let createNewWidgetUrl = `${OPINIONSTAGE_GUTENBERG_DATA.createNewWidgetUrl}&w_type=${backendWidgetTypeForNewWidget(currentWidgetType)}`
  let contentViewEditStatOs = (
    <div className="os-widget-wrapper components-placeholder">
      <p className="components-heading">
        <img src={OPINIONSTAGE_GUTENBERG_DATA.brandLogoUrl} alt="Opinionstage Logg"/>
      </p>
      <a className="opinionstage-button opinionstage-button__blue" href='#' onClick={selectWidget}>Select
        Item</a>
      <a href={createNewWidgetUrl} target="_blank"  rel="noopener"
         className="opinionstage-button opinionstage-button__blue">Create a {currentWidgetTitle}</a>
    </div>
  )
  if (
    embedUrl 
    && embedUrl !== ''
    && buttonText === 'Change' 
  ) {
    contentViewEditStatOs = (
      <div className="os-widget-wrapper components-placeholder">
        <p className="components-heading">
          <img src={OPINIONSTAGE_GUTENBERG_DATA.brandLogoUrl} alt="Opinionstage Logg"/>
        </p> 
        <div className="components-preview__block">
          <div className="components-preview__leftBlockImage">
            <img src={insertItemImage} alt={insertItemOsTitle} className="image"/>
            <div className="overlay">
              <div className="text">
                <a href={insertItemOsView} className='opinionstage-button opinionstage-button__blue' target="_blank"  rel="noopener">View</a>
                <a href={insertItemOsEdit} className='opinionstage-button opinionstage-button__blue' target="_blank"  rel="noopener">Edit</a>
                <a href={insertItemOsStatistics} className='opinionstage-button opinionstage-button__blue' target="_blank"  rel="noopener">Statistics</a>
                <a href='#' className='opinionstage-button opinionstage-button__blue' onClick={selectWidget}>{buttonText}</a>
              </div>
            </div>
          </div>
          <div className="components-preview__rightBlockContent">
            <div className="components-placeholder__label">{currentWidgetTitle}: {insertItemOsTitle}</div>
          </div>
        </div>
      </div>
    )
  } else {
    setAttributes({buttonText: 'Embed'})
  }

  return (
    <div className={className}>
      {contentViewEditStatOs}
    </div>
  )
}

function widgetTypeFromBlockName(blockName) {
  switch (blockName) {
    case 'opinion-stage/block-os-poll':
      return WIDGET_POLL
      break
    case 'opinion-stage/block-os-survey':
      return WIDGET_SURVEY
      break
    case 'opinion-stage/block-os-trivia':
      return WIDGET_TRIVIA_QUIZ
      break
    case 'opinion-stage/block-os-personality':
      return WIDGET_PERSONALITY_QUIZ
      break
    default:
      console.warn('unknown block name:', blockName)
  }
}

// opposite to widgetTypeFromBlockName
function blockName(widgetType) {
  switch (widgetType) {
    case WIDGET_POLL:
      return 'opinion-stage/block-os-poll'
      break
    case WIDGET_SURVEY:
      return 'opinion-stage/block-os-survey'
      break
    case WIDGET_TRIVIA_QUIZ:
      return 'opinion-stage/block-os-trivia'
      break
    case WIDGET_PERSONALITY_QUIZ:
      return 'opinion-stage/block-os-personality'
      break
    default:
      console.warn('unknown block widget type:', widgetType)
  }
}

function widgetTitleFromType(widgetType) {
  switch (widgetType) {
    case WIDGET_POLL:
      return __('Poll')
      break
    case WIDGET_SURVEY:
      return __('Form / Survey')
      break
    case WIDGET_TRIVIA_QUIZ:
      return __('Trivia Quiz')
      break
    case WIDGET_PERSONALITY_QUIZ:
      return __('Personality Quiz')
      break
  }
}

function backendWidgetTypeForNewWidget(widgetType) {
  switch (widgetType) {
    case WIDGET_POLL:
      return 'poll'
      break
    case WIDGET_SURVEY:
      return 'survey'
      break
    case WIDGET_TRIVIA_QUIZ:
      return 'quiz'
      break
    case WIDGET_PERSONALITY_QUIZ:
      return 'outcome'
      break
  }
}

function backendWidgetTypeForViewTemplates(widgetType) {
  switch (widgetType) {
    case WIDGET_POLL:
      return 'polls'
      break
    case WIDGET_SURVEY:
      return 'surveys'
      break
    case WIDGET_TRIVIA_QUIZ:
      return 'trivia_quizzes'
      break
    case WIDGET_PERSONALITY_QUIZ:
      return 'personality_quizzes'
      break
  }
}

// backend API endpoint returns these widget types:
function backendWidgetTypeToBlockWidgetType(backendType) {
  switch (backendType) {
    case 'poll':
      return WIDGET_POLL
      break
    case 'survey':
      return WIDGET_SURVEY
      break
    case 'trivia':
      return WIDGET_TRIVIA_QUIZ
      break
    case 'personality':
      return WIDGET_PERSONALITY_QUIZ
      break
  }
}

function contentPopupWidgetType(widgetType) {
  switch (widgetType) {
    case WIDGET_POLL:
      return OpinionStage.contentPopup.WIDGET_POLL
      break
    case WIDGET_SURVEY:
      return OpinionStage.contentPopup.WIDGET_SURVEY
      break
    case WIDGET_TRIVIA_QUIZ:
      return OpinionStage.contentPopup.WIDGET_TRIVIA_QUIZ
      break
    case WIDGET_PERSONALITY_QUIZ:
      return OpinionStage.contentPopup.WIDGET_PERSONALITY_QUIZ
      break
  }
}
