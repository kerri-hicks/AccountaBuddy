-- AccountaBuddy PostgreSQL Schema

CREATE TABLE IF NOT EXISTS users (
    id               TEXT PRIMARY KEY,   -- Discord user ID (snowflake)
    discord_username TEXT NOT NULL,
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS guild_members (
    id           BIGSERIAL PRIMARY KEY,
    guild_id     TEXT NOT NULL,
    user_id      TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    display_name TEXT NOT NULL,
    updated_at   TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (guild_id, user_id)
);

CREATE INDEX IF NOT EXISTS idx_guild_members_guild_user ON guild_members (guild_id, user_id);

CREATE TABLE IF NOT EXISTS server_config (
    guild_id                  TEXT PRIMARY KEY,
    accountability_channel_id TEXT NOT NULL,
    timezone                  TEXT NOT NULL DEFAULT 'UTC',
    created_at                TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS goals (
    id               BIGSERIAL PRIMARY KEY,
    user_id          TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    guild_id         TEXT NOT NULL,
    name             TEXT NOT NULL,
    description      TEXT,
    personality      TEXT NOT NULL,                       -- hype_coach | dry_colleague | sarcastic_friend | harsh_critic
    cadence_type     TEXT NOT NULL,                       -- one_time | daily | weekly_x | weekly_once | monthly_x | monthly_once
    cadence_target   INTEGER NOT NULL DEFAULT 1,          -- completions required per cycle
    cadence_day      INTEGER,                             -- 0=Sun … 6=Sat, null if not specified
    checkin_time     TIME NOT NULL,                       -- stored as UTC time-of-day
    cycle_start_date DATE,                                -- null for one_time goals
    status           TEXT NOT NULL DEFAULT 'active',      -- active | paused | on_hold | cancelled | completed
    streak_count     INTEGER NOT NULL DEFAULT 0,
    streak_best      INTEGER NOT NULL DEFAULT 0,
    reminder_count   INTEGER NOT NULL DEFAULT 0,          -- day counter for one_time goals
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    cancelled_at     TIMESTAMPTZ,
    completed_at     TIMESTAMPTZ,                         -- set when one_time goal is marked done
    overachiever_finish_out BOOLEAN NOT NULL DEFAULT FALSE -- true = finish out cycle after early completion
);

CREATE INDEX IF NOT EXISTS idx_goals_user_guild_status ON goals (user_id, guild_id, status);
CREATE INDEX IF NOT EXISTS idx_goals_guild_status       ON goals (guild_id, status);

CREATE TABLE IF NOT EXISTS checkins (
    id               BIGSERIAL PRIMARY KEY,
    goal_id          INTEGER NOT NULL REFERENCES goals(id) ON DELETE CASCADE,
    scheduled_at     TIMESTAMPTZ NOT NULL,
    responded_at     TIMESTAMPTZ,
    status           TEXT NOT NULL DEFAULT 'pending',  -- pending | complete | missed | skipped
    escalation_level INTEGER NOT NULL DEFAULT 0,       -- 0=initial, 1=escalated, 2=missed
    cycle_date       DATE NOT NULL,
    discord_message_id TEXT                            -- message ID of the public check-in post
);

CREATE INDEX IF NOT EXISTS idx_checkins_goal_scheduled  ON checkins (goal_id, scheduled_at);
CREATE INDEX IF NOT EXISTS idx_checkins_status_scheduled ON checkins (status, scheduled_at);

CREATE TABLE IF NOT EXISTS cycles (
    id           BIGSERIAL PRIMARY KEY,
    goal_id      INTEGER NOT NULL REFERENCES goals(id) ON DELETE CASCADE,
    start_date   DATE NOT NULL,
    end_date     DATE NOT NULL,
    target       INTEGER NOT NULL,
    completions  INTEGER NOT NULL DEFAULT 0,
    status       TEXT NOT NULL DEFAULT 'active'  -- active | completed | missed
);

CREATE INDEX IF NOT EXISTS idx_cycles_goal_status   ON cycles (goal_id, status);
CREATE INDEX IF NOT EXISTS idx_cycles_end_date      ON cycles (end_date, status);

CREATE TABLE IF NOT EXISTS streak_appeals (
    id         BIGSERIAL PRIMARY KEY,
    goal_id    INTEGER NOT NULL REFERENCES goals(id) ON DELETE CASCADE,
    user_id    TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    expires_at TIMESTAMPTZ NOT NULL,
    status     TEXT NOT NULL DEFAULT 'open',  -- open | approved | denied
    discord_message_id TEXT                   -- message ID of the appeal post
);

CREATE TABLE IF NOT EXISTS appeal_votes (
    id            BIGSERIAL PRIMARY KEY,
    appeal_id     INTEGER NOT NULL REFERENCES streak_appeals(id) ON DELETE CASCADE,
    voter_user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    voted_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (appeal_id, voter_user_id)
);

CREATE TABLE IF NOT EXISTS interactions (
    id               BIGSERIAL PRIMARY KEY,
    actor_user_id    TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    target_goal_id   INTEGER NOT NULL REFERENCES goals(id) ON DELETE CASCADE,
    message_id       TEXT,
    interaction_type TEXT NOT NULL,   -- comment | reply | reaction
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_interactions_actor     ON interactions (actor_user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_interactions_goal      ON interactions (target_goal_id, created_at);

CREATE TABLE IF NOT EXISTS badges (
    id           BIGSERIAL PRIMARY KEY,
    user_id      TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    guild_id     TEXT NOT NULL,
    badge_type   TEXT NOT NULL,    -- most_encouraging | most_relentless | the_ghost | iron_streak | comeback_kid | overachiever
    period_type  TEXT NOT NULL,    -- weekly | monthly | quarterly | annual
    period_start DATE NOT NULL,
    period_end   DATE NOT NULL,
    awarded_at   TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_badges_guild_period ON badges (guild_id, period_type, period_start);
CREATE INDEX IF NOT EXISTS idx_badges_user         ON badges (user_id, guild_id);
