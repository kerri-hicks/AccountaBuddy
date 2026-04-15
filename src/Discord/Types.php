<?php

declare(strict_types=1);

namespace AccountaBuddy\Discord;

class Types
{
    // Interaction types
    const PING              = 1;
    const APPLICATION_COMMAND = 2;
    const MESSAGE_COMPONENT = 3;
    const MODAL_SUBMIT      = 5;

    // Interaction callback types
    const PONG                           = 1;
    const CHANNEL_MESSAGE_WITH_SOURCE    = 4;
    const DEFERRED_CHANNEL_MESSAGE       = 5;
    const DEFERRED_UPDATE_MESSAGE        = 6;
    const UPDATE_MESSAGE                 = 7;
    const MODAL                          = 9;

    // Message flags
    const FLAG_EPHEMERAL = 64;

    // Component types
    const COMPONENT_ACTION_ROW   = 1;
    const COMPONENT_BUTTON       = 2;
    const COMPONENT_STRING_SELECT = 3;
    const COMPONENT_TEXT_INPUT   = 4;

    // Button styles
    const BUTTON_PRIMARY   = 1;
    const BUTTON_SECONDARY = 2;
    const BUTTON_SUCCESS   = 3;
    const BUTTON_DANGER    = 4;

    // Text input styles
    const TEXT_SHORT     = 1;
    const TEXT_PARAGRAPH = 2;

    // Command option types
    const OPTION_SUB_COMMAND = 1;
    const OPTION_STRING      = 3;
    const OPTION_INTEGER     = 4;

    // Personality keys
    const PERSONALITY_HYPE      = 'hype_coach';
    const PERSONALITY_DRY       = 'dry_colleague';
    const PERSONALITY_SARCASTIC = 'sarcastic_friend';
    const PERSONALITY_HARSH     = 'harsh_critic';

    // Cadence types
    const CADENCE_ONE_TIME      = 'one_time';
    const CADENCE_DAILY         = 'daily';
    const CADENCE_WEEKLY_X      = 'weekly_x';
    const CADENCE_WEEKLY_ONCE   = 'weekly_once';
    const CADENCE_MONTHLY_X     = 'monthly_x';
    const CADENCE_MONTHLY_ONCE  = 'monthly_once';

    // Goal statuses
    const GOAL_ACTIVE    = 'active';
    const GOAL_PAUSED    = 'paused';
    const GOAL_ON_HOLD   = 'on_hold';
    const GOAL_CANCELLED = 'cancelled';
    const GOAL_COMPLETED = 'completed';

    // Check-in statuses
    const CHECKIN_PENDING   = 'pending';
    const CHECKIN_COMPLETE  = 'complete';
    const CHECKIN_MISSED    = 'missed';
    const CHECKIN_SKIPPED   = 'skipped';

    // Cycle statuses
    const CYCLE_ACTIVE    = 'active';
    const CYCLE_COMPLETED = 'completed';
    const CYCLE_MISSED    = 'missed';

    // Appeal statuses
    const APPEAL_OPEN     = 'open';
    const APPEAL_APPROVED = 'approved';
    const APPEAL_DENIED   = 'denied';

    // Milestone day thresholds
    const MILESTONES = [7, 14, 30, 90, 180, 365];

    // Escalation order (personality key → next personality key)
    const ESCALATION_ORDER = [
        self::PERSONALITY_HYPE      => self::PERSONALITY_DRY,
        self::PERSONALITY_DRY       => self::PERSONALITY_SARCASTIC,
        self::PERSONALITY_SARCASTIC => self::PERSONALITY_HARSH,
        self::PERSONALITY_HARSH     => self::PERSONALITY_HYPE,
    ];

    // Badge types
    const BADGE_ENCOURAGING  = 'most_encouraging';
    const BADGE_RELENTLESS   = 'most_relentless';
    const BADGE_GHOST        = 'the_ghost';
    const BADGE_IRON_STREAK  = 'iron_streak';
    const BADGE_COMEBACK_KID = 'comeback_kid';
    const BADGE_OVERACHIEVER = 'overachiever';

    // Badge period types
    const PERIOD_WEEKLY    = 'weekly';
    const PERIOD_MONTHLY   = 'monthly';
    const PERIOD_QUARTERLY = 'quarterly';
    const PERIOD_ANNUAL    = 'annual';
}
