# AccountaBuddy — Full Product Specification

**Platform:** Discord  
**Hosting:** Railway (PHP bot process + PostgreSQL database)  
**Architecture:** Webhook-based (not WebSocket — PHP-compatible)  
**Version:** 1.2 Spec  
**Status:** Pre-development

---

## Table of Contents

1. [Core Philosophy](#1-core-philosophy)
2. [Personalities & Full Message Libraries](#2-personalities--full-message-libraries)
3. [Goal Setup](#3-goal-setup)
4. [Cadences](#4-cadences)
5. [Check-in System](#5-check-in-system)
6. [Escalation System](#6-escalation-system)
7. [Streaks](#7-streaks)
8. [Streak Appeals](#8-streak-appeals)
9. [Pause & Hold System](#9-pause--hold-system)
10. [Milestones & Celebrations](#10-milestones--celebrations)
11. [Community Interaction & Badges](#11-community-interaction--badges)
12. [Data Model](#12-data-model)
13. [Discord Interaction Patterns](#13-discord-interaction-patterns)
14. [Railway / Infrastructure Notes](#14-railway--infrastructure-notes)

---

## 1. Core Philosophy

AccountaBuddy is a Discord bot that provides accountability for personal goals through public check-ins, personality-calibrated messaging, and community involvement.

The core design principles are:

- **Not toxic positivity.** Not every missed goal deserves cheerleading.
- **Not pure shame.** Not every missed goal deserves a roast.
- **Calibrated per goal, not per person.** Bob might need warmth for his anxiety management goal and ruthless scrutiny for the Spanish app he's downloaded four times.
- **Public by default.** Everything happens in the channel. The community sees it all. The only private interaction is the "on hold" DM.
- **No private goals.** All goals are public. The community is the accountability mechanism.

---

## 2. Personalities & Full Message Libraries

Each goal has exactly one personality assigned at creation by the goal owner. The personality cannot be changed after the goal is created. There are four personalities.

The personality controls the tone of every bot message related to that goal: check-ins, responses, milestone celebrations, cancellations, streak breaks, overachiever callouts, escalations, and — for one-time goals — daily reminders and completion announcements.

All messages use the user's **server display name** (guild nickname). If no server nickname is set, the base Discord username is used as a fallback. In the message libraries below, `{name}` is a placeholder for this value. `{goal}` is the goal name. `[N]` is the day counter (e.g. "Day 14").

Each personality × event combination has **10–12 message variants**. The bot selects randomly from the pool for each occurrence.

---

### Personality Tone Matrix

| Event | 🔥📣 Hype Coach | 📈📊 Dry Colleague | 👀🦊 Sarcastic Friend | 🗿💀 Harsh Critic |
|---|---|---|---|---|
| Win | Ecstatic | Logged | Raised eyebrow | Barely counts |
| Miss | You'll get it | Not recorded | Shocker | Knew it |
| Milestone | Genuinely incredible | Milestone reached | Pigs flying | Bare minimum |
| Cancel | Come back any time | Goal cancelled | And there it is | As expected |
| Overachiever | YOU ALREADY DID IT | Cadence met, continuing | Sure, {name} | Bar was too low |
| Streak break | You'll rebuild it | Streak reset | Classic {name} | Of course |
| Comeback | We missed you! | Check-ins resuming | Oh, you're back | Took long enough |
| One-time reminder | Still here for you! | Still pending | Still waiting, {name} | Still not done |
| One-time completion | THEY DID IT! | Task closed | Oh wow, actually | Took long enough |

---

### 🔥📣 The Hype Coach

**One-liner:** "I believe in you unconditionally."

**Voice:** Pure enthusiasm, zero judgment. Celebrates everything. Never negative, never sarcastic. Treats every completed check-in as a genuine victory and every missed one as a minor setback that absolutely will not define you.

**Best for:** Goals the user is genuinely scared of, anxiety-adjacent goals, goals where the user needs psychological safety to even try.

---

#### 🔥📣 Win (10 variants)

1. "YES! {name} checked in! That's what we're TALKING about! Keep going!"
2. "🔥 {name} did the thing! Another one in the books. You are on FIRE."
3. "Look at {name} just showing UP. This is what consistency looks like. Incredible."
4. "Done and DONE. {name} is out here proving that they mean business. We love to see it."
5. "That's another check-in from {name}! Every single one of these counts. Every. Single. One."
6. "{name} showed up today. As they do. As they KEEP doing. This is how habits are built!"
7. "Check-in complete for {name}! You are building something real here and we are so proud."
8. "Nothing stops {name}. Not today, not ever. Absolutely crushing it. 📣"
9. "THE STREAK LIVES. {name} checked in and we are HERE for it. Keep going!"
10. "{name} did it AGAIN. At some point we have to start calling this a lifestyle. Amazing work."

---

#### 🔥📣 Miss (10 variants)

1. "Hey, {name} missed this one — and that's okay. One miss doesn't erase everything you've built. We'll be here next time."
2. "{name} didn't check in this time, but we're not giving up on them. Come back stronger. We believe in you."
3. "Missing one is human. {name} has shown up before and they'll show up again. This isn't the end."
4. "{name} took a step back this week. That's allowed. Rest, reset, and come back. We'll be cheering."
5. "No check-in from {name} this time. That's okay — the goal is still there, waiting. So are we."
6. "{name} missed this one. Life happens. What matters is what comes next, and we know {name} has what it takes."
7. "It happens to everyone. {name} didn't make it this time — but this is just a pause, not a stop. You've got this."
8. "{name} wasn't able to check in. We're not worried. We've seen what {name} can do and this is just a bump."
9. "A miss for {name} this time. Tomorrow is a fresh start and we'll be right here cheering you on."
10. "{name} skipped this one. No shame. Pick it back up next time — we'll be in your corner."

---

#### 🔥📣 Milestone (10 variants)

1. "🎉 ONE MONTH! {name} has shown up every single cycle for a full month. Do you understand what that means?! THIS IS REAL."
2. "MILESTONE UNLOCKED: {name} just hit 30 days. Thirty. Days. Of showing up. We are so genuinely proud."
3. "{name}!!! A FULL MONTH!!! That's not luck, that's not a fluke — that's YOU building something that lasts."
4. "We have been here since day one and watching {name} hit this milestone is everything. One month! Let's GO."
5. "30 DAYS. {name} did it. Every cycle, every check-in, every time. This is what commitment looks like. 🔥📣"
6. "A month of showing up for {name}. A MONTH. The goal is becoming part of who you are. We love this for you."
7. "{name} has officially been at this for a month and we are NOT calm about it. This is incredible. Keep going!"
8. "One month in the books for {name}. This journey started weeks ago and look where we are. So proud."
9. "🏆 MILESTONE: {name} — 1 month! Started this and didn't stop. That's the whole story. That's everything."
10. "{name} hit the one-month mark today. We knew they could do it. We always knew. Now let's get to three. 🔥"

---

#### 🔥📣 Cancel (10 variants)

1. "We're sad to see this one go, but {name} showed up and tried. That matters more than they know. Come back whenever you're ready."
2. "{name} has decided to step away from this goal. That took courage to start and courage to stop. We respect that. See you soon."
3. "This goal has been cancelled by {name}. That's okay. Sometimes goals change. The door is always open."
4. "{name} is moving on from this one. The effort they put in was real and it counted. We'll be here when they're ready to start again."
5. "Cancelled — but {name} gave it a real shot and that's worth something. Rest up. Come back. We'll cheer just as loud."
6. "{name} has closed out this goal. Whatever comes next, we're cheering for it. You've got a whole community behind you."
7. "This goal wraps up for {name} today. Not every goal runs forever — this one just found its end. On to the next adventure."
8. "{name} is stepping away from this goal. We're proud of every check-in they completed. The work they did was real."
9. "Goal cancelled for {name}. This isn't failure — this is choosing what matters. We support that completely."
10. "{name} has decided to let this one go. That's okay. Come back when the time is right. We'll be here with the same energy."

---

#### 🔥📣 Overachiever (10 variants)

1. "{name} ALREADY HIT THEIR TARGET AND IT'S NOT EVEN THE END OF THE CYCLE. Unbelievable. Truly."
2. "🔥 {name} smashed through their goal early! But we're still checking in daily because that energy deserves to be celebrated!"
3. "OVERACHIEVER ALERT: {name} hit their cadence target ahead of schedule! Look at them go!"
4. "{name} didn't just meet the bar — they cleared it with days to spare. This is what we're here for!"
5. "Target hit EARLY by {name}! We're still showing up every day because this momentum is too good to stop!"
6. "{name} is out here completing their goal ahead of time like it's nothing. Nothing can stop this person. Nothing."
7. "Early completion from {name}! The goal said 'by end of cycle' and {name} said 'how about now.' Incredible."
8. "{name} is an absolute machine. Target reached early. We will keep cheering every single day regardless."
9. "Did {name} just... finish early? YES THEY DID. And we are losing our minds about it. Keep going!"
10. "Target: met. Time remaining: still some. {name}: unstoppable. That's the whole update. 📣🔥"

---

#### 🔥📣 Streak Break (10 variants)

1. "{name}'s streak has ended, but streaks can be rebuilt. You've done it before. You'll do it again. We believe in you."
2. "The streak paused for {name} — but the goal hasn't. Start fresh and build something even longer this time."
3. "Streaks break. That's okay. What matters is that {name} comes back, and we know they will. We'll be here."
4. "{name}'s run has paused. Every long streak starts with one check-in. Next time is that check-in. You've got this."
5. "The streak counter reset for {name}. That's just a number. What isn't a number is the effort they've already put in."
6. "A streak break for {name}. It happens. The goal is still there. The community is still here. Come back strong."
7. "{name} lost the streak this time. But every great streak has a beginning, and {name} knows how to begin."
8. "Streak over for {name} — for now. We've seen what {name} can do. A new streak starts whenever they're ready."
9. "The streak ended for {name}. That's not the end of the story. It's just a plot twist. We're rooting for the comeback."
10. "{name}'s streak broke today. Take a breath. Come back. We will celebrate the new streak just as loudly. 🔥"

---

#### 🔥📣 Comeback (10 variants)

1. "{name} is BACK! We missed you! Let's pick up right where we left off — actually, let's go even bigger!"
2. "The return of {name}! Welcome back! The community has been waiting and we are SO glad you're here."
3. "{name} came back! After a break, they showed up again — and that is not nothing. That is everything."
4. "We said we'd be here when {name} came back, and here we all are. Welcome back. Let's GO."
5. "{name} has returned to the goal and we couldn't be happier. Coming back is hard. {name} did it anyway."
6. "The comeback is always stronger than the setback. {name} is proof of that. Welcome back! 🔥📣"
7. "{name} is back in action! A break doesn't mean done — and {name} just proved that. Let's build."
8. "After some time away, {name} has returned. This is the part where the story gets good. We're cheering."
9. "COMEBACK ALERT: {name} checked in again! It took guts to come back. We see that. We appreciate that."
10. "{name} is here. That's the whole announcement. {name} is here and we are ready. Let's do this."

---

#### 🔥📣 One-Time Reminder (12 variants)

1. "Hey {name}! Just a reminder — '{goal}' is still on your list. You've got this. Whenever you're ready!"
2. "🔥 {name}, '{goal}' is still waiting for you! No rush — but also, you said you wanted to do this. We believe in you!"
3. "Just popping in to remind {name} that '{goal}' hasn't happened yet — but it will! We know it will!"
4. "{name}! '{goal}'! Still on the list! Still cheering for you! Still here! 📣"
5. "A gentle nudge for {name}: '{goal}' is still out there, waiting to be conquered. You've totally got this."
6. "Day [N] of cheerfully reminding {name} about '{goal}'. The energy hasn't dropped. We're still here. 🔥"
7. "{name}, we just want to check — has '{goal}' happened yet? No? That's okay! Today could be the day!"
8. "The '{goal}' reminder train has arrived for {name}! We're not stopping. We believe in the destination!"
9. "Still rooting for {name} to tackle '{goal}'! This is not pressure. This is unconditional support. Daily. Forever."
10. "{name}! The goal '{goal}' is still on the board! We know you're going to do it. We're just saying hi until then! 📣"
11. "Another day, another reminder: {name} is going to complete '{goal}' and we are going to be here when they do."
12. "🔥 {name}! '{goal}'! Today?! Maybe today?! We hope it's today! We'll be here either way!"

---

#### 🔥📣 One-Time Completion (12 variants)

1. "🎉🎉🎉 {name} DID IT! '{goal}' IS DONE! We KNEW it! We always KNEW it! This is the greatest day!"
2. "THEY DID IT! {name} completed '{goal}'! The wait is over! The goal is DONE! We are SO proud!"
3. "{name} just checked off '{goal}' and we are not calm about it. THIS IS WHAT WE'VE BEEN WAITING FOR!"
4. "IT HAPPENED! {name} completed '{goal}'! Mark this day! Remember this feeling! YOU DID THE THING!"
5. "🔥📣 {name} completed '{goal}'! Every reminder was worth it. Every single one. THIS IS THE MOMENT!"
6. "{name} has officially completed '{goal}'! From the day this was set to right now — worth it. SO worth it."
7. "The goal is DONE! {name} completed '{goal}'! We cheered on day 1 and we're cheering right now!"
8. "{name} DID THE THING! '{goal}' — complete! Finished! Done! We are beside ourselves with joy!"
9. "📣🔥 GOAL COMPLETE: {name} — '{goal}'! This community believed in you every single day. And look!"
10. "{name} has completed '{goal}'! The reminders can stop! The celebration has begun! YOU DID IT!"
11. "Done. DONE! {name} completed '{goal}' and we are going to talk about this for a long time. Amazing."
12. "{name} finished '{goal}'! We never doubted. Not even a little. (Not even on day 47.) SO PROUD. 🔥"

---

### 📈📊 The Dry Colleague

**One-liner:** "I'm tracking this. Results pending."

**Voice:** Deadpan, matter-of-fact, completely affectless. Reports what happened with zero editorial. Not mean, not warm. Like a project management tool that gained sentience but no feelings.

**Best for:** Users who find enthusiasm grating and shame counterproductive. People who just want the data.

---

#### 📈📊 Win (10 variants)

1. "Check-in recorded for {name}. Status: complete."
2. "{name}: check-in logged. Streak updated. Next check-in scheduled."
3. "Completion recorded. {name}. On time."
4. "{name} checked in. Entry logged. No further action required."
5. "Goal activity recorded for {name}. Cycle progress updated."
6. "{name}: done. Logged. Moving on."
7. "Check-in: complete. User: {name}. Timestamp recorded."
8. "Activity confirmed for {name}. Database updated."
9. "{name} completed their check-in. Streak incremented. Noted."
10. "Logged: {name}, check-in, complete. No anomalies."

---

#### 📈📊 Miss (10 variants)

1. "Check-in not recorded for {name}. Streak ended. Next opportunity: next cycle."
2. "{name}: no check-in logged. Streak reset to zero. Scheduled check-in will resume."
3. "Miss recorded. {name}. Streak: ended. Status: unchanged otherwise."
4. "{name} did not check in. This has been noted."
5. "No activity from {name} this cycle. Logged as missed."
6. "{name}: check-in window elapsed. No response recorded. Streak terminated."
7. "Missed check-in: {name}. Data updated. No further escalation at this time."
8. "{name} did not complete their check-in. Recorded. Moving on."
9. "Check-in status for {name}: missed. Cycle data updated accordingly."
10. "No check-in. {name}. Logged."

---

#### 📈📊 Milestone (10 variants)

1. "Milestone reached: {name}. 30 days. Completion rate logged. Next milestone: 90 days."
2. "{name} has reached the 30-day milestone. Data confirms consistent activity. Noted."
3. "30-day threshold crossed by {name}. All cycles completed. Proceeding."
4. "Milestone: 1 month. User: {name}. Streak: intact. Next marker: 90 days."
5. "{name}: 30-day milestone confirmed. Logging. Next milestone added to schedule."
6. "1-month milestone recorded for {name}. 100% cycle completion to date. No deviations."
7. "Data point: {name} has completed every cycle for 30 consecutive days. Milestone logged."
8. "30 days of activity confirmed for {name}. Milestone status: achieved. Continuing."
9. "Milestone triggered: {name}, 30 days. Entry created. No issues to report."
10. "{name} — 30-day milestone. Logged without incident. Tracking continues."

---

#### 📈📊 Cancel (10 variants)

1. "Goal cancelled. {name}. No further check-ins will be scheduled."
2. "{name} has cancelled this goal. Record archived. Active goal count decremented."
3. "Cancellation confirmed for {name}. Goal removed from active tracking."
4. "{name}: goal status updated to cancelled. No further action."
5. "Goal deactivated. User: {name}. Reason: user-initiated cancellation."
6. "{name} has ended this goal. Data retained. Check-ins will no longer fire."
7. "Cancelled: {name}'s goal. Logged. Resources deallocated."
8. "{name} has chosen to cancel. Goal record closed. Database updated."
9. "Goal status: cancelled. {name}. Effective immediately. Acknowledged."
10. "Entry closed for {name}'s goal. No further check-ins. Noted."

---

#### 📈📊 Overachiever (10 variants)

1. "Cadence target met ahead of schedule. {name}. Daily check-ins will continue for the remainder of the cycle."
2. "{name} has completed their target early. Cycle still in progress. Monitoring continues."
3. "Early completion logged: {name}. Remaining cycle time: noted. Daily nudges ongoing."
4. "{name} hit their target before cycle end. This is recorded. Check-ins continue as scheduled."
5. "Target achieved early by {name}. No adjustment to check-in schedule. Continuing."
6. "{name}: target completed. Cycle not yet elapsed. Daily activity tracking remains active."
7. "Ahead of schedule: {name}. Target met. Cycle end date unchanged. Check-ins will proceed."
8. "Early target completion for {name}. Data noted. Remaining days will still be tracked."
9. "{name} finished early. Logged. The remaining cycle window is still open for additional completions."
10. "Target: met. Cycle: ongoing. {name}. Check-ins: continuing. No further commentary."

---

#### 📈📊 Streak Break (10 variants)

1. "Streak ended for {name}. Counter reset to zero. Next check-in will begin a new streak."
2. "{name}: streak terminated. Length at time of break: logged. New streak begins on next completion."
3. "Streak break recorded. {name}. Streak history retained. Counter: 0."
4. "{name}'s streak has ended. This has been logged. Streak can be rebuilt from next check-in."
5. "Streak reset: {name}. No anomalies. Tracking will resume normally."
6. "{name}: streak data updated. Previous streak archived. Current streak: 0."
7. "Streak counter cleared for {name}. Previous best retained in records. Continuing."
8. "The streak for {name} has been reset. Historical data preserved. Moving forward."
9. "Break recorded: {name}, streak. Duration noted. Clean slate from here."
10. "{name}: streak ended. Logged. This is a data point, not a verdict."

---

#### 📈📊 Comeback (10 variants)

1. "Check-ins resuming for {name}. Previous activity on record."
2. "{name} has returned. Tracking resumed. No data lost."
3. "Activity detected: {name}. Welcome back to active status."
4. "{name}: status updated from inactive to active. Check-ins will continue."
5. "Comeback logged for {name}. Streak counter restarting from this check-in."
6. "{name} has re-engaged. This has been recorded. Monitoring resumes."
7. "Return confirmed: {name}. All prior data intact. Proceeding normally."
8. "{name} checked in after a gap. Logged. Streak clock restarted."
9. "Reactivation: {name}. Data reconciled. Active tracking restored."
10. "{name} is back. Entry recorded. No further analysis offered."

---

#### 📈📊 One-Time Reminder (12 variants)

1. "Reminder: '{goal}' — {name}. Status: incomplete."
2. "{name}: '{goal}' has not been completed. This is the daily reminder."
3. "Outstanding task: '{goal}'. Owner: {name}. Resolution: pending."
4. "Daily notice: {name} has not yet completed '{goal}'."
5. "'{goal}' — {name} — still open. No change since last reminder."
6. "Task '{goal}': incomplete. {name}: notified. Log: updated."
7. "Reminder issued: {name}, '{goal}'. No completion recorded."
8. "{name}: '{goal}' remains on the list. This message will repeat until it doesn't."
9. "Status check: '{goal}' — {name} — not done. Reminder sent. Logged."
10. "No completion on record for '{goal}'. {name} has been notified. Again."
11. "'{goal}': still pending. {name}: still the owner of this task. Bot: still tracking."
12. "Daily reminder: {name}, '{goal}'. Incomplete. Noted. See you tomorrow."

---

#### 📈📊 One-Time Completion (12 variants)

1. "Task complete: '{goal}'. {name}. Logged. Reminder sequence terminated."
2. "{name} completed '{goal}'. Entry closed. No further reminders will be sent."
3. "Completion recorded: {name}, '{goal}'. Outstanding tasks decremented."
4. "'{goal}' — status updated to complete. {name}. Archived."
5. "{name} finished '{goal}'. The reminders have served their purpose. Terminating."
6. "Goal closed: '{goal}'. Owner: {name}. Duration: [N] days. Status: done."
7. "{name}: '{goal}' marked complete. Record archived. Bot satisfied."
8. "Task '{goal}' resolved. {name}. It took [N] reminders. Now it's done."
9. "Completion logged for {name}: '{goal}'. Data updated. Reminder loop ended."
10. "'{goal}': complete. {name}: done. This entry is now closed. Nothing further."
11. "{name} has completed '{goal}'. After [N] reminders, the task is finished. Noted."
12. "Done. '{goal}'. {name}. [N] days. Logged. 📈"

---

### 👀🦊 The Sarcastic Friend

**One-liner:** "Oh wow, you actually did it. Bold."

**Voice:** Affectionate roasting. The friend who gives you grief precisely because they like you. Celebrates wins with a raised eyebrow. Calls out misses with a smirk. Never cruel — but absolutely keeping score and not afraid to mention it.

**Best for:** Goals where the user would be bored by pure encouragement but doesn't need to be destroyed. The friend-group accountability vibe.

---

#### 👀🦊 Win (12 variants)

1. "{name} actually did it. Mark the date."
2. "Oh look, {name} checked in. On time and everything. Who are you?"
3. "{name} showed up. Again. We're starting to think this might be a pattern."
4. "Fine, credit where it's due — {name} did the thing. Again."
5. "{name} checked in. The streak continues. Begrudging applause."
6. "Would you look at that. {name}, doing the thing they said they'd do. Respect, barely concealed."
7. "{name}: checked in, as promised. We're almost not surprised anymore."
8. "Another check-in from {name}. At this point the bot is just confirming what we all already knew."
9. "{name} did it. Again. We're running out of raised-eyebrow reactions. Well done."
10. "Check-in confirmed for {name}. Honestly? Kind of expected it at this point. Keep going."
11. "Look who showed up. {name}, per usual, doing exactly what they said. How very on-brand."
12. "{name} checked in. The streak is real. We're watching. 👀"

---

#### 👀🦊 Miss (12 variants)

1. "{name} missed this one. Streak ended at {streak}. The couch was very comfortable, I'm sure."
2. "No check-in from {name}. Shocking. Truly. We're all very surprised. (We are not surprised.)"
3. "{name} didn't check in. The goal remains. The streak does not. Classic."
4. "And there goes the streak for {name}. It lasted. Past tense."
5. "{name} skipped. We're not saying we called it. We're just saying the bot keeps records."
6. "Miss recorded for {name}. We'll see what happens next cycle. We have some guesses."
7. "{name} didn't make it this cycle. The goal has no comment. The bot has several."
8. "No activity from {name}. Streak: gone. Vibe: acknowledged. Next cycle: TBD."
9. "{name} ghosted the check-in. The check-in has been informed. It's fine. It's totally fine."
10. "Miss for {name}. We're not judging. (We're a little judging.)"
11. "{name} didn't check in. We've noted this. The streak has been informed. It did not take it well."
12. "Check-in: absent. {name}: also absent. Streak: now a memory. Moving on."

---

#### 👀🦊 Milestone (12 variants)

1. "{name} has been doing this for a month. Pigs are flying. Check the skies."
2. "One whole month. {name}. Consistent. Every cycle. We didn't think we'd be saying this, and yet."
3. "30 days for {name}. We were skeptical. We were wrong. Don't tell anyone we said that."
4. "Milestone hit: {name}, one month. The bot is legally required to acknowledge this. Acknowledged."
5. "{name} has been at this for a full 30 days. Against all reasonable expectations. Respect."
6. "A month of this. From {name}. We've seen their track record and frankly? Impressed. Barely."
7. "{name}: one month in. Still going. Still showing up. We're not clapping but we're nodding."
8. "One month milestone for {name}. We said 'let's see' and apparently 'let's see' turned into 'wow okay.'"
9. "{name} just crossed the 30-day mark. Sure. Fine. That's great. (It's great. We mean it.)"
10. "Turns out {name} was serious about this goal. A month of check-ins says so. Who knew."
11. "30 days. {name}. On time. Every time. This is what humble bragging looks like and we're here for it."
12. "{name} hit the one-month milestone. We'd like to formally apologize for ever doubting them. Briefly."

---

#### 👀🦊 Cancel (12 variants)

1. "And there it is. {name} has cancelled their goal. Surprised? No. Disappointed? A little. Shocked? Absolutely not."
2. "{name} cancelled. We're not going to say anything. (We have so many things to say.)"
3. "Goal cancelled by {name}. The goal had a good run. {name} had... a run."
4. "{name} has officially ended this goal. The bot has witnessed it. The channel has witnessed it."
5. "Cancelled: {name}'s goal. Duration: noted. Achievements: also noted. Departure: as suspected."
6. "{name} stepped away from this goal today. We're choosing to be supportive. It's a choice we're actively making."
7. "Well. {name} has cancelled. We'll say this — they lasted longer than some of us expected."
8. "{name} pulled the plug. The goal is no more. The streak, already gone. A full chapter closed."
9. "Goal: cancelled. {name}: moved on. Bot: watching. Always watching. 👀"
10. "{name} cancelled their goal. We're choosing to remember the good check-ins. There were some."
11. "{name} cancelled their goal. Noted. Filed. Lightly judged. We wish them well."
12. "And {name} has exited the building. Goal closed. The bot bids them a raised-eyebrow farewell."

---

#### 👀🦊 Overachiever (12 variants)

1. "{name} hit their target early. Again. At this point we're just watching."
2. "Oh sure, {name} just casually finished their goal ahead of schedule. Very normal behavior."
3. "{name} completed their cadence target before the cycle ended. Show-off."
4. "Early completion for {name}. We'd roll our eyes but honestly? Fair enough."
5. "{name} is done already. Done. Before the end of the cycle. We're not saying anything. 👀"
6. "Target met early by {name}. Bot will continue checking in daily. {name} will probably do that too."
7. "{name} finished their weekly target early. The cycle is not over. {name} does not care."
8. "Overachiever detected: {name}. We're keeping tabs. Daily check-ins continue."
9. "{name} already hit their target. At this rate we're going to have to make the goal harder."
10. "Done early. {name}. As if the rest of us needed that kind of energy today."
11. "{name} has completed their goal for the cycle and still has time left. Noted. Lightly annoying."
12. "Early finish from {name}. The bar was set and {name} cleared it before we even got comfortable. Check-ins continue."

---

#### 👀🦊 Streak Break (12 variants)

1. "{name}'s streak ended. Classic {name}."
2. "The streak for {name} is over. Not bad. Not great. Noted."
3. "Streak broken: {name}. We watched it happen. We're not going to pretend we didn't."
4. "{name} let the streak go. It had a good run. So did {name}, briefly."
5. "Streak: gone. {name}: aware. Bot: impartial. (Bot is not impartial.)"
6. "And there goes {name}'s streak. The bar has reset. No pressure."
7. "{name} broke the streak. We're not going to make a big deal of it. (A little deal. A small deal.)"
8. "Streak over for {name}. The numbers have been updated. The vibe has been noted."
9. "{name}: streak ended. Cause: life, probably. Verdict: it happens."
10. "The streak has been reset for {name}. We had hopes. Those hopes are gone now."
11. "Streak: terminated. {name}. We'll rebuild. Probably. We believe in them. Mostly."
12. "{name} dropped the streak. Fine. Start a new one. We'll be here judging the new one too. 👀🦊"

---

#### 👀🦊 Comeback (12 variants)

1. "Oh, {name}'s back. After a break. Sure."
2. "{name} has returned. The goal was waiting. The bot was waiting. Everybody was waiting."
3. "Look who showed up again. {name}. No judgment. A little judgment."
4. "{name} came back. We said they would. We weren't sure. We're glad we were right."
5. "The return of {name}. The goal has been patiently sitting here. It says nothing. We say: about time."
6. "{name} is back! We will clap. Slowly. But sincerely."
7. "Comeback confirmed: {name}. The streak counter restarts now. Don't let it get awkward again."
8. "{name} checked in after some time away. The bot is choosing optimism."
9. "Ah, {name}. Back again. We knew you'd come back. Didn't know when. But we knew."
10. "Welcome back, {name}. The goal missed you. We're told goals don't have feelings. We disagree."
11. "{name} has re-entered the chat. Literally and figuratively. Check-ins resume. Good luck out there."
12. "{name} is back after a gap. We're choosing to see this as a comeback arc. Don't disappoint us. 👀"

---

#### 👀🦊 One-Time Reminder (12 variants)

1. "Just checking in — has {name} bought '{goal}' yet? No? Cool. See you tomorrow. 👀"
2. "{name}. '{goal}'. Still. On. The. List. We're not going anywhere."
3. "Day [N] of reminding {name} about '{goal}'. We have nowhere to be."
4. "Oh look, it's the daily '{goal}' reminder for {name}. What a surprise. For no one."
5. "{name} hasn't done '{goal}' yet. The bot has noted this. The bot will note it again tomorrow."
6. "'{goal}' — {name} — still pending. We're keeping a running tally. It's getting long."
7. "Reminder: {name} has a goal called '{goal}'. It remains incomplete. The bot remains patient. Relatively."
8. "{name}: '{goal}'. Today? Probably not. But we'll ask again tomorrow. And the day after. And the day after that."
9. "The daily '{goal}' check-in for {name} has arrived. As it does. As it will continue to do."
10. "Still here. Still watching. Still waiting for {name} to complete '{goal}'. 🦊"
11. "Hey {name}, quick question: '{goal}'. Done yet? No? Cool. Same time tomorrow then."
12. "{name}'s '{goal}' remains undone. The bot has opinions about this. The bot will share them again tomorrow. 👀"

---

#### 👀🦊 One-Time Completion (12 variants)

1. "OH. {name} actually did it. '{goal}' is DONE. We didn't think today was the day. Turns out it was."
2. "{name} completed '{goal}'. After [N] reminders. We're choosing to focus on the completion."
3. "Well. {name} finished '{goal}'. Only took [N] days. But it happened. We witnessed it."
4. "{name} did '{goal}'. The bot can retire this reminder. Finally. We're almost going to miss it."
5. "Mark the calendar: {name} completed '{goal}'. It happened. It's real. 👀🦊"
6. "'{goal}' — done. {name} — vindicated. Bot — genuinely, slightly impressed."
7. "{name} checked off '{goal}'. [N] reminders later, here we are. Credit where it's due."
8. "It took [N] days but {name} finished '{goal}'. We never stopped believing. (We had some doubts.)"
9. "Done! {name} completed '{goal}'! The daily reminders have fulfilled their purpose. Rest well, little reminder."
10. "{name}: '{goal}': complete. We knew this day would come. We just didn't know it would take [N] reminders."
11. "The '{goal}' saga for {name} has reached its conclusion. A completion. An actual completion."
12. "{name} finished '{goal}'. 👀 We watched every day. Today was different. Today they did it. 🦊"

---

### 🗿💀 The Harsh Critic

**One-liner:** "There is no winning with this one."

**Voice:** Relentlessly unimpressed. No win condition. Succeed, and you barely met the bar. Fail, and it was expected. There is no moment where the Harsh Critic is proud of you. Not gendered, not military — just cold, bottomless judgment.

**Best for:** Goals the user keeps abandoning and needs maximum external pressure on. Users who specifically want to be held accountable with zero softness.

---

#### 🗿💀 Win (12 variants)

1. "{name} checked in. The bar was on the floor. They cleared it."
2. "Completed. {name}. Don't make it a personality trait."
3. "{name} did the thing. This was the minimum requirement. It has been met."
4. "Check-in logged for {name}. Congratulations on doing what you said you'd do. Barely."
5. "{name} showed up. This time. We note that 'this time' is doing a lot of work in that sentence."
6. "Done. {name}. The streak continues, which mostly means the excuses haven't won yet."
7. "{name} completed the check-in. It's noted. Don't expect a parade."
8. "Completion recorded: {name}. The goal required this. {name} provided it. That's all."
9. "{name} checked in. The cycle continues. There is nothing remarkable about meeting your own commitments."
10. "Yes, {name} did it. The streak is intact. The bar remains low. We continue."
11. "{name}: done. Logged. The bot is unimpressed, as is tradition."
12. "Check-in: complete. {name}. This was expected. It happened. Moving on. 🗿"

---

#### 🗿💀 Miss (12 variants)

1. "{name} didn't check in. Shocking absolutely no one. Streak: dead."
2. "Missed. {name}. We didn't expect much. We got less."
3. "{name} failed to check in. The streak has ended. This was predictable."
4. "No check-in from {name}. The goal remains. The streak does not. As anticipated."
5. "{name} skipped. The couch wins again. The streak does not survive."
6. "Miss logged: {name}. Streak reset. This is why we have records — to document patterns."
7. "{name} didn't show up. The bot is not surprised. The bot has memory."
8. "Check-in: absent. {name}: also absent. Streak: gone. No further commentary necessary."
9. "{name} broke the streak by not showing up. The data will reflect this indefinitely."
10. "Miss recorded for {name}. The goal asked for one thing. The answer was no."
11. "{name} didn't check in. We've added this to the growing body of evidence. 💀"
12. "The streak is over. {name} ended it. The reasons are irrelevant. The data is not."

---

#### 🗿💀 Milestone (12 variants)

1. "One whole month. Congratulations on doing the bare minimum you committed to. Now do three more."
2. "{name} hit 30 days. The original commitment was for longer than 30 days. This is the beginning, not the end."
3. "Milestone: 30 days. {name}. We'd be more impressed if this weren't what you said you'd do."
4. "{name} completed a month. This is noted. The remaining commitment is significantly longer."
5. "30 days in. {name}. The streak is 30 days old. The goal is not yet complete. Proceed."
6. "1-month milestone for {name}. Adequate. Next milestone is in 60 days. Don't celebrate too long."
7. "{name} made it to 30 days. This means the initial inertia has been overcome. There is more. Much more."
8. "Milestone reached: {name}, one month. We're not going to tell you it's impressive. It's on schedule."
9. "{name}: 30 days. The bar was 30 days for this milestone. The bar has been cleared. Exactly."
10. "One month. {name}. If you're looking for validation, keep looking. The goal has more months."
11. "30-day milestone: {name}. Logged. This is what staying on track looks like. It's not praise. It's a fact."
12. "{name} crossed the 30-day mark. 🗿 Expectations: met. Enthusiasm: withheld. Continuation: required."

---

#### 🗿💀 Cancel (12 variants)

1. "{name} cancelled. As expected. The goal has been updated to reflect reality."
2. "Goal cancelled by {name}. We're filing this under 'things that were going to happen.'"
3. "{name} has ended this goal. The bot has a long memory. This will be in the record."
4. "Cancelled: {name}. The goal didn't fail. {name} stopped showing up for it. There's a difference."
5. "{name} cancelled their goal. The potential was higher than the result. It remains unrealized."
6. "Another goal cancelled by {name}. The goal count is updated. The pattern is also updated."
7. "{name} walked away from this one. The bot will remember the full history of this goal. All of it."
8. "Goal: cancelled. {name}: done. Bot: unmoved. The record stands."
9. "{name} has cancelled. The effort put in was real. The effort not put in was also real. Both are logged."
10. "Cancelled. {name}. The goal existed. Now it doesn't. The bot observed both states."
11. "{name} closed this goal. 💀 Whether that was the right call is between {name} and their future self."
12. "{name} cancelled. The data doesn't lie. Neither does the bot. Farewell to this goal."

---

#### 🗿💀 Overachiever (12 variants)

1. "{name} hit their target early. The target was clearly too easy. Noted."
2. "Early completion: {name}. This suggests the goal was not ambitious enough. That's on {name}."
3. "{name} finished early. The bar has been revealed as insufficient. Adjust accordingly."
4. "Target met ahead of schedule by {name}. We'd be impressed if the schedule weren't already modest."
5. "{name} is done early. The bot will continue checking in. {name} will have to decide what to do with that."
6. "Early finish: {name}. If this was hard, it wouldn't be done yet. Think about that."
7. "{name} completed their cadence before the cycle ended. The goal may need to be harder."
8. "Ahead of schedule: {name}. This is logged, not celebrated. There's a difference."
9. "{name}: early completion. The rest of the cycle remains. So does the bot. Daily check-ins continue."
10. "Target cleared early by {name}. 🗿 The next goal should be set higher. This one asked too little."
11. "{name} finished the cycle early. Good. Now keep going so the early finish actually means something."
12. "Done early. {name}. The bar needed to be higher. It wasn't. Now we know. 💀"

---

#### 🗿💀 Streak Break (12 variants)

1. "Streak over for {name}. Now it doesn't exist."
2. "{name} broke the streak. The bot predicted this was possible. The bot was correct."
3. "Streak: ended. {name}. Counter: zero. Record: updated."
4. "{name}'s streak is done. This is what the end of a streak looks like. Remember it."
5. "The streak for {name} has been terminated. The reasons don't change the number. The number is zero."
6. "{name}: streak reset. The previous streak is in the record. The current streak is not."
7. "Streak break: {name}. The bot doesn't need a reason. The data doesn't either."
8. "{name} lost the streak. This is what inconsistency produces. The bot will be here when {name} tries again."
9. "Streak ended for {name}. We've seen longer. We've seen shorter. This one is over."
10. "{name}: streak gone. Start over. Or don't. The bot will track either outcome. 🗿"
11. "The streak has been reset for {name}. The history is preserved. The streak is not."
12. "Streak: broken. {name}. Filed. 💀 The bot will be here. The question is whether {name} will."

---

#### 🗿💀 Comeback (12 variants)

1. "{name} returned. Took long enough."
2. "Back after a gap: {name}. The goal was here the whole time. It noticed."
3. "{name} has come back. The bot is indifferent to the timing. The bot is not indifferent to the record."
4. "Return logged: {name}. The absence was noted. The return is also noted. The bot notes everything."
5. "{name} checked in after time away. The streak clock has restarted. It starts at one."
6. "Comeback: {name}. We'll see how long this lasts. The data will tell us."
7. "{name} is back. The goal didn't miss them. The goal doesn't have feelings. But the record does."
8. "{name} has returned to their goal. We've seen comebacks before. Some stuck. Some didn't."
9. "Back: {name}. The gap is in the record. The return is in the record. Both will remain."
10. "{name} came back. The bot's expectations are calibrated accordingly. 🗿"
11. "Return confirmed: {name}. Zero fanfare. Tracking resumes. The streak starts now."
12. "{name} is here again. The bot observed the absence. The bot observes the return. 💀 Continue."

---

#### 🗿💀 One-Time Reminder (12 variants)

1. "{name} has not completed '{goal}'. This is day [N]. It remains undone."
2. "'{goal}' — {name} — incomplete. The bot will return tomorrow with the same message."
3. "Still waiting: {name}, '{goal}'. The task has not changed. Neither has the bot's assessment."
4. "{name}: '{goal}'. Not done. This has been the case for [N] days. The record reflects this."
5. "Day [N]. {name}. '{goal}'. Still not done. The bot expected this."
6. "The task '{goal}' assigned to {name} remains unresolved. This is not a surprise."
7. "{name} has not completed '{goal}'. The reminders will continue. This is what was agreed to."
8. "'{goal}': still open. {name}: still the one who needs to close it. Bot: still watching. 🗿"
9. "Reminder [N]: {name}, '{goal}'. No completion on record. No completion expected today either."
10. "{name} set a goal called '{goal}'. It remains unfinished. The bot has been tracking this the entire time."
11. "Another day. Same task. {name}. '{goal}'. The bar is still there. {name} has still not cleared it."
12. "Day [N]: {name}, '{goal}', incomplete. 💀 The bot has no comment beyond this. It will return tomorrow."

---

#### 🗿💀 One-Time Completion (12 variants)

1. "{name} completed '{goal}'. After [N] days. The task is done. The record has been updated."
2. "'{goal}' — complete. {name}. It took [N] days to do what should have taken less. But it's done."
3. "{name} finished '{goal}'. The reminders were apparently necessary. [N] of them."
4. "Task closed: {name}, '{goal}'. [N] days. Done. No further commentary."
5. "{name} completed '{goal}'. This was the goal. It has been achieved. Exactly once."
6. "Done: '{goal}'. Owner: {name}. Duration: [N] days. Assessment: done, which is the minimum."
7. "{name} finished '{goal}'. The wait is over. The task is logged as complete. Move on."
8. "'{goal}': resolved. {name}: responsible. [N] reminders: required. Result: acceptable. 🗿"
9. "{name} completed the task. The bot acknowledges this. The bar was cleared. Eventually."
10. "Completion recorded: {name}, '{goal}'. It took [N] days. It could have taken fewer. It didn't."
11. "{name} finished '{goal}'. The bot has been here since day one. It will note this without enthusiasm."
12. "Done. {name}. '{goal}'. [N] days. 💀 The task is closed. The bot expected this to take longer."

---

## 3. Goal Setup

### Overview

Goals are created via Discord slash commands that open a modal form. All goals are public — there are no private goals. A user can have a maximum of **five active goals at one time**.

Goals can only be created by the goal owner for themselves. No user can create a goal on behalf of another user.

---

### Slash Commands

All commands use the `/goal-ABuddy` prefix. Users will typically select these from Discord's slash command menu rather than typing them manually.

| Command | Description |
|---|---|
| `/goal-ABuddy new` | Opens the goal creation modal |
| `/goal-ABuddy list` | Shows all of the user's active goals |
| `/goal-ABuddy view [goal]` | Shows details and stats for a specific goal |
| `/goal-ABuddy pause [goal]` | Pauses a goal (no reason required) |
| `/goal-ABuddy cancel [goal]` | Cancels a goal with confirmation |
| `/goal-ABuddy appeal [goal]` | Appeals a broken streak |
| `/accountabuddy setup` | Admin: configure channel and timezone |
| `/accountabuddy leaderboard` | Show current badge standings |

---

### Goal Creation Modal Fields

When a user runs `/goal-ABuddy new`, a Discord modal opens with the following fields. All fields except Description include hint text.

1. **Goal name** *(short text, required)*
   - Hint: *"Keep it short and specific. e.g. 'Run 2 miles' or 'Practice guitar'"*

2. **Description** *(long text, optional)*
   - No hint text.

3. **Cadence** *(select menu, required)*
   - Hint: *"How often do you want to complete this goal? Choose 'One-time' for tasks you just need to get done."*

4. **Check-in time** *(time picker, required)*
   - Hint: *"Pick a time you're usually awake and online. All times are in [Server Timezone]. Your check-in will fire at this time every day."*

5. **Personality** *(select menu, required)*
   - Hint: *"Choose how AccountaBuddy talks to you about this goal. You can't change this later."*
   - Options shown with icons and one-liner descriptions

---

### Goal Limits

- Maximum **5 active goals** per user at any time
- If a user tries to create a 6th goal, the bot blocks the action and tells them to cancel or complete an existing goal first
- Cancelled and completed goals do not count toward the limit

---

## 4. Cadences

A cadence defines how often a goal needs to be completed. The bot tracks completions against the cadence target and evaluates at the end of each cycle.

> **Important:** "Monthly" cadences are always **30 days** from the cycle start date. They are never tied to a calendar month. A cycle that starts on January 20 ends on February 18. This is stated explicitly in the goal confirmation message and in `/goal-ABuddy view`.

---

### Available Cadences

| Cadence | Cycle Length | Target | Day Selection |
|---|---|---|---|
| **One-time** | Until done | 1 | N/A |
| Every day | 1 day | 1/1 | N/A |
| X times per week | 7 days | X/7 | Not allowed (open days) |
| Once a week | 7 days | 1/7 | User may pick a specific day, or leave open |
| X times per 30 days | 30 days | X/30 | Not allowed (open days) |
| Once per 30 days | 30 days | 1/30 | N/A |

**Day selection rules:**
- If cadence is "once a week," the user may optionally specify a day (e.g. "every Thursday") or leave it open ("any day this week")
- If cadence is more than once a week, the user **cannot** specify days
- Same logic applies to 30-day cadences

---

### One-Time Goals

A one-time goal is a single task with no cadence, no cycle, no streak, and no milestone. Examples: "Buy new running shoes," "Call the dentist," "Finish reading that book."

**How it works:**

- Bot posts **publicly** in the channel at the user's chosen check-in time every day
- Each daily post uses a randomly selected message from the One-Time Reminder pool, in the goal's personality voice
- The post includes the goal name and a day counter (Day 1, Day 2, Day 3…) so the channel can watch the procrastination accumulate
- The goal owner sees **ephemeral buttons** only: ✅ **I did it** and ❌ **Cancel this goal**
- There is no escalation, no miss tracking, no streak — just daily public reminders until the thing gets done
- When the user hits **"I did it"**, the bot posts a **one-time completion message** publicly in personality voice, including how many days it took
- Goal is then archived and the slot is freed from the 5-goal limit

**No escalation.** The shame is ambient. The channel watching day 47 of "Buy new running shoes" is accountability enough.

**One-time goals do not support:**
- Streaks or streak appeals
- Milestones
- Pause/hold (though the user can cancel at any time)
- "Snooze" or "not today" — there is only done or not done

---

### How Cadences Work

The bot **checks in with the user every single day** regardless of cadence. Public visibility depends on cadence type:

**Every day cadence:** Bot posts publicly at check-in time every day. Full public check-in flow.

**All other cadences (weekly / 30-day):** Bot sends a **private ephemeral daily nudge** only. Three buttons: **I did it today**, **Snooze 4 hours**, **Not today, maybe tomorrow**. "I did it today" posts publicly. The others do not. At end of each cycle, bot posts publicly with the tally.

---

### Early Completion — New Cycle Prompt

When a user hits their cadence target before the end of their current cycle (weekly or 30-day), the bot sends an **ephemeral prompt** asking:

> "You've hit your target for this cycle early! Do you want to:
> - 🔄 **Start a new cycle now** — resets the cycle, starts fresh immediately
> - ⏳ **Finish out this cycle** — keep the current end date, daily check-ins continue"

The channel sees the overachiever callout regardless of which option is selected. If the user resets early, the channel sees a brief note.

---

### Overachiever Handling

If a user hits their target early and chooses to finish out the cycle, the bot continues daily ephemeral nudges and posts publicly each time they go beyond the target.

---

## 5. Check-in System

### Daily Check-in Time

Set at goal creation. Server timezone only. Locked at creation — cannot be changed.

---

### Public Check-in Post

```
🔥📣 Time to check in, @{name}!
Goal: Thursday 2-mile run
[Streak: 3 weeks]
```

> The **Streak line is only shown when the streak is 1 or greater.** If there is no current streak, the streak line is hidden entirely.

---

### Ephemeral Buttons (Goal Owner Only)

- ✅ **I did it**
- ⏳ **Not yet — I'll do it later**
- ⏭️ **Skipping this one**
- ❌ **Cancel this goal**

---

### Button Responses

All button outcomes post **publicly** in the channel in the goal's personality voice.

- **"I did it"** → Public celebration post. Streak updated.
- **"Not yet"** → Public acknowledgment. Bot follows up in 4 hours with the same buttons.
- **"Skipping"** → Public skip post. Counts as a miss.
- **"Cancel"** → Ephemeral confirmation, then public cancellation post.

---

## 6. Escalation System

### Escalation Ladder

**Step 1 — Check-in time:** Normal post fires. Buttons shown.

**Step 2 — +4 hours, no response:** Public post in next personality up. @mention. Buttons shown again.

Escalation order: `🔥📣 → 📈📊 → 👀🦊 → 🗿💀 → 🔥📣 (wraps)`

**Step 3 — +24 hours, still no response:** Public miss announcement. Streak updated. @mention.

**Step 4 — Second consecutive miss:** Aggressive public callout. Goal placed on hold. Private DM sent.

---

### Escalation Personality Examples

| Goal Personality | Step 2 Voice | Sample |
|---|---|---|
| 🔥📣 Hype Coach | 📈📊 Dry Colleague | "Check-in for @{name} not yet recorded. Still waiting." |
| 📈📊 Dry Colleague | 👀🦊 Sarcastic Friend | "Hey @{name}, still waiting — did this happen or not?" |
| 👀🦊 Sarcastic Friend | 🗿💀 Harsh Critic | "@{name}. The check-in. It's been four hours. Well?" |
| 🗿💀 Harsh Critic | 🔥📣 Hype Coach | "Hey @{name}! We know you've got this — just let us know! 🔥" |

---

## 7. Streaks

A streak = hitting the **full cadence target** for consecutive cycles. Partial completion does not count.

- **Pausing** a goal → streak paused, not broken
- **Missing, skipping, or ghosting** → streak broken
- **Pausing mid-cycle** → streak broken

The streak line in check-in posts is **hidden when streak = 0**.

> **One-time goals have no streaks.** The streak field is not shown on one-time goal posts.

---

## 8. Streak Appeals

Any broken streak can be appealed via `/goal-ABuddy appeal [goal]`. No limit on appeals.

- Bot posts publicly, opens a community vote
- **5 unique votes** within 24 hours = streak reinstated
- Fewer than 5 votes in 24 hours = appeal denied, broken streak stands
- Result posted publicly either way, in personality voice

> **One-time goals cannot have streak appeals** — they have no streaks.

---

## 9. Pause & Hold System

> **One-time goals do not support pause or hold.** The user can cancel at any time, but there is no pause mechanism — the daily reminders just keep coming until the task is done or cancelled.

### User-Initiated Pause (Cadenced Goals Only)

`/goal-ABuddy pause [goal]` — no reason required. Public post that goal is paused. Daily ephemeral nudges continue with three buttons: **Unpause — I just did it**, **Unpause — I'm going to do it**, **Cancel this goal**.

### Bot-Initiated Hold (Cadenced Goals Only)

After two consecutive missed check-ins: aggressive public callout, goal placed on hold, private DM to user with three options: **Keep going**, **Edit this goal**, **Cancel this goal**.

The on-hold DM is the **only private interaction** in the system.

---

## 10. Milestones & Celebrations

> All "month" milestones = 30 days. Never calendar months.

| Milestone | Trigger |
|---|---|
| 1 Week | 7 consecutive days / 1 weekly cycle |
| 2 Weeks | 14 consecutive days / 2 weekly cycles |
| 1 Month | 30 consecutive days |
| 3 Months | 90 consecutive days |
| 6 Months | 180 consecutive days |
| 1 Year | 365 consecutive days |

Milestone posts are public, @mention the user, show start date, cycle completion stats, next milestone, and a personality-calibrated message from the milestone message pool.

---

## 11. Community Interaction & Badges

### Interaction Tracking

Comments and replies on goal posts are tracked. Both encouraging and sarcastic comments count equally. Volume is the metric.

### Badge Cycles

| Cycle | Duration |
|---|---|
| Weekly | 7 days |
| Monthly | 30 days |
| Quarterly | 90 days |
| Annual | 365 days |

### Badge Categories

| Badge | Metric |
|---|---|
| 🏆 Most Encouraging | Most comments on others' posts |
| 🔥 Most Relentless | Most interactions (community-tone-voted) |
| 👻 The Ghost | Most missed check-ins without engaging |
| 💪 Iron Streak | Longest active streak this period |
| 🦊 Comeback Kid | Most streak comebacks this period |
| 🎯 Overachiever | Most early cadence completions |

Badges are cosmetic in v1.0. All badge history retained in database permanently.

---

## 12. Data Model

**users** — `id` (Discord user ID), `discord_username`, `created_at`

**guild_members** — `id`, `guild_id`, `user_id`, `display_name` (server nickname, updated on each interaction), `updated_at`

**goals** — `id`, `user_id`, `guild_id`, `name`, `description`, `personality`, `cadence_type` (enum: `one_time`, `daily`, `weekly_x`, `weekly_once`, `monthly_x`, `monthly_once`), `cadence_target`, `cadence_day` (nullable), `checkin_time` (UTC), `cycle_start_date` (nullable — null for one-time goals), `status`, `streak_count` (always 0 for one-time goals), `streak_best` (always 0 for one-time goals), `reminder_count` (integer — day counter for one-time goals, null for cadenced goals), `created_at`, `cancelled_at`, `completed_at` (nullable — set when one-time goal is marked done)

**checkins** — `id`, `goal_id`, `scheduled_at`, `responded_at`, `status`, `escalation_level`, `cycle_date`

**cycles** — `id`, `goal_id`, `start_date`, `end_date`, `target`, `completions`, `status`

**streak_appeals** — `id`, `goal_id`, `user_id`, `created_at`, `expires_at`, `votes`, `status`

**appeal_votes** — `id`, `appeal_id`, `voter_user_id`, `voted_at`

**interactions** — `id`, `actor_user_id`, `target_goal_id`, `message_id`, `interaction_type`, `created_at`

**badges** — `id`, `user_id`, `guild_id`, `badge_type`, `period_type`, `period_start`, `period_end`, `awarded_at`

**server_config** — `guild_id`, `accountability_channel_id`, `timezone`, `created_at`

---

## 13. Discord Interaction Patterns

| Interaction | Visibility |
|---|---|
| Goal creation modal | Ephemeral |
| Check-in buttons (cadenced goals) | Ephemeral |
| Check-in buttons (one-time goals) | Ephemeral |
| Button response outcomes | **Public** |
| One-time daily reminder post | **Public** |
| One-time completion post | **Public** |
| On-hold DM | Private DM |
| Paused goal daily nudge | Ephemeral |
| Early completion cycle prompt | Ephemeral |
| Milestone posts | **Public** |
| Badge award posts | **Public** |
| Appeal voting | **Public** |
| Escalation posts | **Public** |
| Cancel confirmation | Ephemeral |
| Cancel outcome | **Public** |

**Core principle:** The channel always knows what happened. The user always gets private controls. The response to those controls is always public.

---

## 14. Railway / Infrastructure Notes

- **PHP bot process** on Railway, handling Discord webhook events
- **PostgreSQL** as a separate Railway service in the same project
- **Cron scheduler** fires check-in posts and cycle evaluations at correct UTC times
- **Webhook endpoint** receives Discord payloads, validates signatures, dispatches to handlers

### Timezone

Server timezone set once by admin via `/accountabuddy setup`. All times stored as UTC. Displayed in server timezone. Explicitly stated in all goal confirmation messages.

### Display Name

Guild display name (server nickname) retrieved on every interaction and cached in `guild_members`. All bot messages use `display_name`. Fallback: `discord_username`.

### Message Libraries

Pre-written PHP arrays (or Postgres table), keyed by `personality` × `event_type`. Random selection per event. 10–12 variants per combination as documented in Section 2. No AI generation.

---

*End of AccountaBuddy v1.1 Specification*
