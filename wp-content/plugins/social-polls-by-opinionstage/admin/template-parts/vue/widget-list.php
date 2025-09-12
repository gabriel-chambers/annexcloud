<?php
/**
 * Widgets List template.
 *
 * @package OpinionStageWordPressPlugin
 */

use Opinionstage\Infrastructure\Helper;

?>
<div class='page-content'>
	<div class='content-actions content-actions__top'>
		<div class='content-actions__left'>
			<h1 class="main-title"><?php esc_html_e( 'My Items', 'social-polls-by-opinionstage' ); ?></h1>
		</div>
		<div class="content-actions__right">
			<a href="<?php echo esc_url( add_query_arg( 'w_type', 'all', OPINIONSTAGE_REDIRECT_CREATE_WIDGET_API_UTM ) ); ?>" class="opinionstage-button opinionstage-button__blue opinionstage-button__middle" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Create New', 'social-polls-by-opinionstage' ); ?></a>
		</div>
	</div>
	<div class="content-actions content-actions__form">
		<div class="content-actions__left opinionstage-search-form" :class='{ hidden: !showSearch }'>

			<svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M10.0731 5.82675C10.0731 8.09112 8.23751 9.92675 5.97314 9.92675C3.70878 9.92675 1.87314 8.09112 1.87314 5.82675C1.87314 3.56238 3.70878 1.72675 5.97314 1.72675C8.23751 1.72675 10.0731 3.56238 10.0731 5.82675ZM9.71821 9.85474C8.7363 10.7681 7.41996 11.3268 5.97314 11.3268C2.93558 11.3268 0.473145 8.86432 0.473145 5.82675C0.473145 2.78919 2.93558 0.326752 5.97314 0.326752C9.01071 0.326752 11.4731 2.78919 11.4731 5.82675C11.4731 6.91114 11.1593 7.92224 10.6175 8.77417L13.3216 11.4783C13.595 11.7516 13.595 12.1948 13.3216 12.4682C13.0483 12.7416 12.6051 12.7416 12.3317 12.4682L9.71821 9.85474Z" fill="#A0A0A0"/>
			</svg>

			<input
					class='os-search__input'
					placeholder='Search'
					type='search'
					v-model='widgetTitleSearch'
			>
		</div>
		<div class="content-actions__right">
			<div class="dropdown dropdown_items">
				<button class="dropbtn opinionstage-pseudo-chevron"><span>{{ selectedWidgetTitle }}</span></button> 
				<div class="dropdown-content">
					<div class='filter__itm'
						@click="selectWidgetType('all')"
						:class="{ active: selectedWidgetType === 'all' }"
					><?php esc_html_e( 'all items', 'social-polls-by-opinionstage' ); ?></div>
					<div class='filter__itm'
						@click="selectWidgetType('poll')"
						:class="{ active: selectedWidgetType === 'poll' }"
					><?php esc_html_e( 'poll', 'social-polls-by-opinionstage' ); ?></div>
					<div class='filter__itm'
						@click="selectWidgetType('survey')"
						:class="{ active: selectedWidgetType === 'survey' }"
					><?php esc_html_e( 'form / survey', 'social-polls-by-opinionstage' ); ?></div>
					<div class='filter__itm'
						@click="selectWidgetType('trivia')"
						:class="{ active: selectedWidgetType === 'trivia' }"
					><?php esc_html_e( 'trivia quiz', 'social-polls-by-opinionstage' ); ?></div>
					<div class='filter__itm'
						@click="selectWidgetType('outcome')"
						:class="{ active: selectedWidgetType === 'outcome' }"
					><?php esc_html_e( 'personality quiz', 'social-polls-by-opinionstage' ); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class='content__list'>
		<div v-if='hasData'>
			<div class='content__itm' v-for="widget in widgets">
				<a <?php echo Helper::get_link_target_blank_attribute(); ?> :href='widget.landingPageUrl'>
					<div class='content__image'>
						<img :src='widget.imageUrl'>
						<div class='content__label'>{{ widget.type }}</div>
					</div>
				</a>
				<div class='content__info'>
					<span v-if="widget.isDraft"
							class="opinionstage-draft"><?php esc_html_e( 'draft', 'social-polls-by-opinionstage' ); ?></span>
					<a <?php echo Helper::get_link_target_blank_attribute(); ?> :href='widget.editUrl'>
						<span class="content__info-title">{{ widget.title }}</span>
						<div class="content__info-details">
							<span class="opinionstage-widget-date">{{ widget.updatedAt | moment('DD MMMM YYYY') }}</span>
							<span class="opinionstage-status opinionstage-status__closed" v-if="widget.isClosed">
								<?php esc_html_e( 'closed', 'social-polls-by-opinionstage' ); ?>
							</span>
							<span class="opinionstage-status opinionstage-status__open" v-if="widget.isOpen">
								<?php esc_html_e( 'open', 'social-polls-by-opinionstage' ); ?>
							</span>
						</div>
					</a>
				</div>
				<?php if ( $is_my_items_admin_page ) { ?>
					<div class="opinionstage-item-action-container">
						<a href="#" @click="select(widget)"
							class="opinionstage-button opinionstage-button__middle"><?php esc_html_e( 'Add to Site', 'social-polls-by-opinionstage' ); ?></a>
						<a :href='widget.editUrl' class="opinionstage-button opinionstage-button__middle"
							<?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Edit', 'social-polls-by-opinionstage' ); ?></a>
						<a :href='widget.statsUrl' class="opinionstage-button opinionstage-button__middle"
							<?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Results', 'social-polls-by-opinionstage' ); ?></a>
					</div>
				<?php } else { ?>
					<div class='content__links'>
						<button class='opinionstage-button opinionstage-button__grey opinionstage-button__middle'
								@click="select(widget)"><?php $is_my_items_admin_page ? esc_html_e( 'Add to site', 'social-polls-by-opinionstage' ) : esc_html_e( 'Insert', 'social-polls-by-opinionstage' ); ?></button>
						<div class="dropdown dropdown-popup-action">
							<div class="popup-action popup-content-btn opinionstage-pseudo-chevron"></div>
							<div class="popup-action-dropdown dropdown-content">
								<a class='content__links-itm' <?php echo Helper::get_link_target_blank_attribute(); ?>
									:href='widget.landingPageUrl'><?php esc_html_e( 'view', 'social-polls-by-opinionstage' ); ?></a>
								<a class='content__links-itm' <?php echo Helper::get_link_target_blank_attribute(); ?> :href='widget.editUrl'
									v-show="!widget.template"><?php esc_html_e( 'edit', 'social-polls-by-opinionstage' ); ?></a>
								<a class='content__links-itm' <?php echo Helper::get_link_target_blank_attribute(); ?> :href='widget.statsUrl'
									v-show="!widget.template"><?php esc_html_e( 'Results', 'social-polls-by-opinionstage' ); ?></a>
							</div>
						</div>
					</div>

				<?php } ?>
			</div>
			<div class='content__loading' v-if='dataLoading'>
				<?php esc_html_e( 'loading...', 'social-polls-by-opinionstage' ); ?>
			</div>
			<div v-else class="opinionstage-load-more">
				<button
					class='opinionstage-button opinionstage-button__blue opinionstage-button__middle'
					v-if='!noMoreData'
					@click='showMore'
				><?php esc_html_e( 'Click for more', 'social-polls-by-opinionstage' ); ?></button>
			</div>
		</div>
		<div v-else>
			<?php esc_html_e( 'No items found', 'social-polls-by-opinionstage' ); ?>
		</div>
	</div>
	<div class="selected-draft" v-if="selectedDraftWidget.editUrl">
		<div class="selected-draft__container">
			<div>
				<span id="opinionstage-dialog-close" class="opinionstage-close" @click="selectedDraftWidget = !selectedDraftWidget"></span>
				<div class="selected-draft__message">
					<p>
						<?php
						printf(
							'%s <a :href="selectedDraftWidget.editUrl" target="_ blank">%s</a> %s',
							esc_html__( 'Widget is not published yet. Please', 'social-polls-by-opinionstage' ),
							esc_html__( 'edit', 'social-polls-by-opinionstage' ),
							esc_html__( 'the widget to publish it', 'social-polls-by-opinionstage' )
						);
						?>
					</p>
					<p>
						<?php esc_html_e( 'Need Help?', 'social-polls-by-opinionstage' ); ?>
						<a href="<?php echo esc_url( OPINIONSTAGE_LIVE_CHAT_URL_UTM ); ?>" <?php echo Helper::get_link_target_blank_attribute(); ?>><?php esc_html_e( 'Contact Us' ); ?></a></p>
				</div>
			</div>
		</div>
	</div>
</div>
