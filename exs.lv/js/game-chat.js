/**
 * EXS.LV Shared Game Chat Engine
 * Real-time unified chat across all games
 */

(function ($) {
	'use strict';

	$(document).ready(function () {
		var $wrapper = $('#game-chat-wrapper');
		if (!$wrapper.length) {
			return;
		}

		var currentGame = $wrapper.data('game') || 'speles';
		var currentGameTitle = $wrapper.data('game-title') || 'Spēles';
		var currentUserId = parseInt($wrapper.data('user-id'), 10) || 0;
		var lastId = 0;
		var pollTimer = null;
		var isPolling = false;
		var isTabVisible = true;
		var normalInterval = 4000;
		var hiddenInterval = 15000;
		var hasInitialLoad = false;

		// Sound preference from localStorage
		var soundEnabled = localStorage.getItem('exs_chat_sound') !== '0';
		updateSoundButtonState();

		// Collapse preference from localStorage
		var isCollapsed = localStorage.getItem('exs_chat_collapsed') === '1';
		if (isCollapsed) {
			$('#game-chat-body').hide();
			$('#chat-toggle-collapse').text('▴').attr('title', 'Izvērst tērzētavu');
		}

		// Players list toggle
		var showPlayers = localStorage.getItem('exs_chat_show_players') === '1';
		if (showPlayers) {
			$('#game-chat-active-bar').show();
			$('#chat-toggle-players').addClass('active');
		}

		var $messagesContainer = $('#game-chat-messages');
		var $scrollBottomBtn = $('#game-chat-scroll-bottom');
		var $chatForm = $('#game-chat-form');
		var $chatInput = $('#game-chat-input');
		var $sendBtn = $('#game-chat-send-btn');
		var $charsLeft = $('#chat-chars-left');
		var $emojisBtn = $('#game-chat-emojis-btn');
		var $emojisPopup = $('#game-chat-emojis-popup');

		// Web Audio API Synthesizer for message notification
		function playNotificationSound() {
			if (!soundEnabled) {
				return;
			}
			try {
				var AudioCtx = window.AudioContext || window.webkitAudioContext;
				if (!AudioCtx) {
					return;
				}
				if (!window._chatAudioCtx) {
					window._chatAudioCtx = new AudioCtx();
				}
				var ctx = window._chatAudioCtx;
				if (ctx.state === 'suspended') {
					ctx.resume();
				}
				var osc = ctx.createOscillator();
				var gain = ctx.createGain();

				osc.type = 'sine';
				osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
				osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.08); // A5

				gain.gain.setValueAtTime(0.06, ctx.currentTime);
				gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);

				osc.connect(gain);
				gain.connect(ctx.destination);

				osc.start();
				osc.stop(ctx.currentTime + 0.13);
			} catch (e) {
				// AudioContext not allowed or unsupported
			}
		}

		function updateSoundButtonState() {
			var $btn = $('#chat-toggle-sound');
			if (soundEnabled) {
				$btn.text('🔊').removeClass('muted').attr('title', 'Skaņa ieslēgta (Klikšķini, lai izslēgtu)');
			} else {
				$btn.text('🔇').addClass('muted').attr('title', 'Skaņa izslēgta (Klikšķini, lai ieslēgtu)');
			}
		}

		// Prevent keyboard events in chat input from affecting game canvas
		if ($chatInput.length) {
			$chatInput.on('keydown keyup keypress', function (e) {
				e.stopPropagation();
			});

			// Character counter
			$chatInput.on('input', function () {
				var max = 400;
				var len = $(this).val().length;
				var left = max - len;
				$charsLeft.text(left);
				if (left < 20) {
					$charsLeft.addClass('warn');
				} else {
					$charsLeft.removeClass('warn');
				}
			});
		}

		// Check if user is scrolled near bottom
		function isUserNearBottom() {
			var el = $messagesContainer[0];
			if (!el) return true;
			return el.scrollHeight - el.scrollTop - el.clientHeight < 70;
		}

		// Smooth scroll to bottom
		function scrollToBottom(force) {
			var el = $messagesContainer[0];
			if (!el) return;
			if (force || isUserNearBottom()) {
				$messagesContainer.stop().animate({ scrollTop: el.scrollHeight }, 200);
				$scrollBottomBtn.fadeOut(150);
			}
		}

		// Scroll event to show/hide "New messages" button
		$messagesContainer.on('scroll', function () {
			if (isUserNearBottom()) {
				$scrollBottomBtn.fadeOut(150);
			}
		});

		$scrollBottomBtn.on('click', function () {
			scrollToBottom(true);
		});

		// Build HTML for a single chat message
		function renderMessageHtml(msg) {
			var myClass = msg.is_me ? ' is-me' : '';
			var delBtnHtml = '';
			if (msg.can_delete) {
				delBtnHtml = '<button type="button" class="chat-msg-del" data-id="' + msg.id + '" title="Dzēst ziņu">✕</button>';
			}

			var html = '<div class="chat-msg-row' + myClass + '" id="chat-msg-' + msg.id + '" data-id="' + msg.id + '" data-user-id="' + msg.user_id + '">';
			html += '  <div class="chat-msg-avatar">';
			html += '    <a href="/user/' + msg.user_id + '"><img src="' + msg.avatar + '" alt="' + msg.nick + '" /></a>';
			html += '  </div>';
			html += '  <div class="chat-msg-body">';
			html += '    <div class="chat-msg-header">';
			html += '      <span class="chat-msg-author">' + msg.author_html + '</span>';
			html += '      <span class="chat-msg-game">' + msg.game_badge + '</span>';
			html += '      <span class="chat-msg-time" title="' + msg.time_str + '">' + msg.time_str + '</span>';
			html +=        delBtnHtml;
			html += '    </div>';
			html += '    <div class="chat-msg-text">' + msg.text + '</div>';
			html += '  </div>';
			html += '</div>';

			return html;
		}

		// Synchronize online status indicator (*) on message authors
		function updateMessagesOnlineStatus(onlineUserIds) {
			if (!onlineUserIds || !Array.isArray(onlineUserIds)) return;
			var onlineMap = {};
			for (var i = 0; i < onlineUserIds.length; i++) {
				onlineMap[onlineUserIds[i]] = true;
			}

			$messagesContainer.find('.chat-msg-row').each(function () {
				var uid = parseInt($(this).data('user-id'), 10);
				if (!uid) return;
				var isOnline = !!onlineMap[uid];
				var $author = $(this).find('.chat-msg-author');
				var $star = $author.find('.r, .lb, .g');

				if (isOnline) {
					if (!$star.length) {
						var $coloredSpan = $author.find('span.admins, span.mods, span.rautors, span.bot').first();
						if (!$coloredSpan.length) {
							$coloredSpan = $author.children('span').last();
						}
						if ($coloredSpan.length) {
							$coloredSpan.prepend('<span class="r">*</span>');
						} else {
							$author.prepend('<span class="r">*</span>');
						}
					}
				} else {
					if ($star.length) {
						$star.remove();
					}
				}
			});
		}

		// Update active players list
		function updateOnlinePlayers(players) {
			var count = players ? players.length : 0;
			$('#game-chat-online-count').text(count);

			var $list = $('#game-chat-players-list');
			if (!count) {
				$list.html('<span class="empty-players-notice">Neviens cits šobrīd nespēlē. Esi pirmais!</span>');
				return;
			}

			var html = '';
			for (var i = 0; i < players.length; i++) {
				var p = players[i];
				var meTag = p.is_me ? ' <span class="tag-you">(Tu)</span>' : '';
				html += '<div class="active-player-pill" title="' + p.nick + ' spēlē ' + p.game_title + '">';
				html += '  <div class="pill-player"><span class="pill-dot"></span> <span class="pill-nick">' + p.author_html + meTag + '</span></div>';
				html += '  <a href="' + p.game_url + '" class="pill-game"><span class="pill-icon">' + p.game_icon + '</span> ' + p.game_title + '</a>';
				html += '</div>';
			}
			$list.html(html);
		}

		// Fetch messages and presence from server
		function pollChat() {
			if (isPolling) {
				return;
			}
			isPolling = true;

			$.ajax({
				url: '/game_chat.php',
				type: 'GET',
				dataType: 'json',
				data: {
					action: 'fetch',
					last_id: lastId,
					game: currentGame,
					game_title: currentGameTitle
				},
				success: function (data) {
					isPolling = false;
					if (!data || !data.success) {
						return;
					}

					var msgs = data.messages || [];
					var shouldPlaySound = false;
					var wasNearBottom = isUserNearBottom();

					if (!hasInitialLoad) {
						hasInitialLoad = true;
						$messagesContainer.empty();
						if (msgs.length === 0) {
							$messagesContainer.html('<div class="chat-empty-state"><span class="empty-icon">💬</span> Čatā pagaidām nav ziņu. Esi pirmais un pasveicini citus spēlētājus!</div>');
						} else {
							var allHtml = '';
							for (var i = 0; i < msgs.length; i++) {
								allHtml += renderMessageHtml(msgs[i]);
							}
							$messagesContainer.html(allHtml);
							scrollToBottom(true);
						}
					} else if (msgs.length > 0) {
						$messagesContainer.find('.chat-empty-state').remove();

						for (var j = 0; j < msgs.length; j++) {
							var m = msgs[j];
							if ($('#chat-msg-' + m.id).length === 0) {
								$messagesContainer.append(renderMessageHtml(m));
								if (!m.is_me) {
									shouldPlaySound = true;
								}
							}
						}

						if (shouldPlaySound) {
							playNotificationSound();
						}

						if (wasNearBottom) {
							scrollToBottom(true);
						} else {
							$scrollBottomBtn.fadeIn(150);
						}
					}

					if (data.last_id && data.last_id > lastId) {
						lastId = data.last_id;
					}

					// Update active online players
					updateOnlinePlayers(data.online_players);

					// Dynamically sync online star (*) on chat messages
					if (data.online_uids) {
						updateMessagesOnlineStatus(data.online_uids);
					}
				},
				error: function () {
					isPolling = false;
				},
				complete: function () {
					scheduleNextPoll();
				}
			});
		}

		function scheduleNextPoll() {
			clearTimeout(pollTimer);
			var delay = isTabVisible ? normalInterval : hiddenInterval;
			pollTimer = setTimeout(pollChat, delay);
		}

		// Tab visibility listener
		if (typeof document.hidden !== 'undefined') {
			document.addEventListener('visibilitychange', function () {
				isTabVisible = !document.hidden;
				if (isTabVisible) {
					pollChat();
				}
			});
		}

		// Initial poll
		pollChat();

		// Submit new message
		if ($chatForm.length) {
			$chatForm.on('submit', function (e) {
				e.preventDefault();
				var text = $.trim($chatInput.val());
				if (!text) {
					return;
				}

				$sendBtn.prop('disabled', true).addClass('loading');

				$.ajax({
					url: '/game_chat.php?action=send',
					type: 'POST',
					dataType: 'json',
					data: {
						message: text,
						game: currentGame,
						game_title: currentGameTitle
					},
					success: function (res) {
						$sendBtn.prop('disabled', false).removeClass('loading');
						if (res && res.success) {
							$chatInput.val('');
							$charsLeft.text('400').removeClass('warn');
							$emojisPopup.hide();

							if (res.message) {
								$messagesContainer.find('.chat-empty-state').remove();
								if ($('#chat-msg-' + res.message.id).length === 0) {
									$messagesContainer.append(renderMessageHtml(res.message));
									scrollToBottom(true);
								}
								if (res.message.id > lastId) {
									lastId = res.message.id;
								}
							}
							$chatInput.focus();
						} else {
							var errMsg = (res && res.error) ? res.error : 'Kļūda nosūtot ziņu.';
							alert(errMsg);
						}
					},
					error: function () {
						$sendBtn.prop('disabled', false).removeClass('loading');
						alert('Tīkla kļūda. Lūdzu pārbaudiet savienojumu.');
					}
				});
			});
		}

		// Delete message handler
		$messagesContainer.on('click', '.chat-msg-del', function (e) {
			e.preventDefault();
			var msgId = $(this).data('id');
			if (!msgId) return;

			if (!confirm('Vai tiešām vēlies dzēst šo ziņu?')) {
				return;
			}

			$.ajax({
				url: '/game_chat.php?action=delete',
				type: 'POST',
				dataType: 'json',
				data: { id: msgId },
				success: function (res) {
					if (res && res.success) {
						var $row = $('#chat-msg-' + msgId);
						$row.fadeOut(200, function () {
							$row.remove();
							if ($messagesContainer.children().length === 0) {
								$messagesContainer.html('<div class="chat-empty-state"><span class="empty-icon">💬</span> Čatā pagaidām nav ziņu.</div>');
							}
						});
					} else {
						alert((res && res.error) ? res.error : 'Neizdevās dzēst ziņu.');
					}
				}
			});
		});

		// Toggle Sound
		$('#chat-toggle-sound').on('click', function (e) {
			e.preventDefault();
			soundEnabled = !soundEnabled;
			localStorage.setItem('exs_chat_sound', soundEnabled ? '1' : '0');
			updateSoundButtonState();
			if (soundEnabled) {
				playNotificationSound();
			}
		});

		// Toggle Collapse / Expand
		$('#chat-toggle-collapse').on('click', function (e) {
			e.preventDefault();
			var $body = $('#game-chat-body');
			if ($body.is(':visible')) {
				$body.slideUp(200);
				$(this).text('▴').attr('title', 'Izvērst tērzētavu');
				localStorage.setItem('exs_chat_collapsed', '1');
			} else {
				$body.slideDown(200, function () {
					scrollToBottom(true);
				});
				$(this).text('▾').attr('title', 'Sakļaut tērzētavu');
				localStorage.setItem('exs_chat_collapsed', '0');
			}
		});

		// Toggle Active Players Bar
		$('#chat-toggle-players').on('click', function (e) {
			e.preventDefault();
			var $bar = $('#game-chat-active-bar');
			if ($bar.is(':visible')) {
				$bar.slideUp(150);
				$(this).removeClass('active');
				localStorage.setItem('exs_chat_show_players', '0');
			} else {
				$bar.slideDown(150);
				$(this).addClass('active');
				localStorage.setItem('exs_chat_show_players', '1');
			}
		});

		// Emojis Picker
		$emojisBtn.on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$emojisPopup.toggle();
		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest('#game-chat-emojis-popup, #game-chat-emojis-btn').length) {
				$emojisPopup.hide();
			}
		});

		$emojisPopup.on('click', '.emoji-opt', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var code = $(this).data('code') || $(this).text();
			if ($chatInput.length) {
				var val = $chatInput.val();
				$chatInput.val(val + (val.length && val.charAt(val.length - 1) !== ' ' ? ' ' : '') + code + ' ');
				$chatInput.trigger('input').focus();
			}
			$emojisPopup.hide();
		});

	});
})(jQuery);
