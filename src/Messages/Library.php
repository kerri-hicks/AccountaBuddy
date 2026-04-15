<?php

declare(strict_types=1);

namespace AccountaBuddy\Messages;

use AccountaBuddy\Discord\Types;

class Library
{
    private static array $messages = [

        // ─────────────────────────────────────────────────────────────────────
        // 🔥📣 HYPE COACH
        // ─────────────────────────────────────────────────────────────────────
        Types::PERSONALITY_HYPE => [

            'win' => [
                "YES! {name} checked in! That's what we're TALKING about! Keep going!",
                "🔥 {name} did the thing! Another one in the books. You are on FIRE.",
                "Look at {name} just showing UP. This is what consistency looks like. Incredible.",
                "Done and DONE. {name} is out here proving that they mean business. We love to see it.",
                "That's another check-in from {name}! Every single one of these counts. Every. Single. One.",
                "{name} showed up today. As they do. As they KEEP doing. This is how habits are built!",
                "Check-in complete for {name}! You are building something real here and we are so proud.",
                "Nothing stops {name}. Not today, not ever. Absolutely crushing it. 📣",
                "THE STREAK LIVES. {name} checked in and we are HERE for it. Keep going!",
                "{name} did it AGAIN. At some point we have to start calling this a lifestyle. Amazing work.",
            ],

            'miss' => [
                "Hey, {name} missed this one — and that's okay. One miss doesn't erase everything you've built. We'll be here next time.",
                "{name} didn't check in this time, but we're not giving up on them. Come back stronger. We believe in you.",
                "Missing one is human. {name} has shown up before and they'll show up again. This isn't the end.",
                "{name} took a step back this week. That's allowed. Rest, reset, and come back. We'll be cheering.",
                "No check-in from {name} this time. That's okay — the goal is still there, waiting. So are we.",
                "{name} missed this one. Life happens. What matters is what comes next, and we know {name} has what it takes.",
                "It happens to everyone. {name} didn't make it this time — but this is just a pause, not a stop. You've got this.",
                "{name} wasn't able to check in. We're not worried. We've seen what {name} can do and this is just a bump.",
                "A miss for {name} this time. Tomorrow is a fresh start and we'll be right here cheering you on.",
                "{name} skipped this one. No shame. Pick it back up next time — we'll be in your corner.",
            ],

            'milestone' => [
                "🎉 ONE MONTH! {name} has shown up every single cycle for a full month. Do you understand what that means?! THIS IS REAL.",
                "MILESTONE UNLOCKED: {name} just hit 30 days. Thirty. Days. Of showing up. We are so genuinely proud.",
                "{name}!!! A FULL MONTH!!! That's not luck, that's not a fluke — that's YOU building something that lasts.",
                "We have been here since day one and watching {name} hit this milestone is everything. One month! Let's GO.",
                "30 DAYS. {name} did it. Every cycle, every check-in, every time. This is what commitment looks like. 🔥📣",
                "A month of showing up for {name}. A MONTH. The goal is becoming part of who you are. We love this for you.",
                "{name} has officially been at this for a month and we are NOT calm about it. This is incredible. Keep going!",
                "One month in the books for {name}. This journey started weeks ago and look where we are. So proud.",
                "🏆 MILESTONE: {name} — 1 month! Started this and didn't stop. That's the whole story. That's everything.",
                "{name} hit the one-month mark today. We knew they could do it. We always knew. Now let's get to three. 🔥",
            ],

            'cancel' => [
                "We're sad to see this one go, but {name} showed up and tried. That matters more than they know. Come back whenever you're ready.",
                "{name} has decided to step away from this goal. That took courage to start and courage to stop. We respect that. See you soon.",
                "This goal has been cancelled by {name}. That's okay. Sometimes goals change. The door is always open.",
                "{name} is moving on from this one. The effort they put in was real and it counted. We'll be here when they're ready to start again.",
                "Cancelled — but {name} gave it a real shot and that's worth something. Rest up. Come back. We'll cheer just as loud.",
                "{name} has closed out this goal. Whatever comes next, we're cheering for it. You've got a whole community behind you.",
                "This goal wraps up for {name} today. Not every goal runs forever — this one just found its end. On to the next adventure.",
                "{name} is stepping away from this goal. We're proud of every check-in they completed. The work they did was real.",
                "Goal cancelled for {name}. This isn't failure — this is choosing what matters. We support that completely.",
                "{name} has decided to let this one go. That's okay. Come back when the time is right. We'll be here with the same energy.",
            ],

            'overachiever' => [
                "{name} ALREADY HIT THEIR TARGET AND IT'S NOT EVEN THE END OF THE CYCLE. Unbelievable. Truly.",
                "🔥 {name} smashed through their goal early! But we're still checking in daily because that energy deserves to be celebrated!",
                "OVERACHIEVER ALERT: {name} hit their cadence target ahead of schedule! Look at them go!",
                "{name} didn't just meet the bar — they cleared it with days to spare. This is what we're here for!",
                "Target hit EARLY by {name}! We're still showing up every day because this momentum is too good to stop!",
                "{name} is out here completing their goal ahead of time like it's nothing. Nothing can stop this person. Nothing.",
                "Early completion from {name}! The goal said 'by end of cycle' and {name} said 'how about now.' Incredible.",
                "{name} is an absolute machine. Target reached early. We will keep cheering every single day regardless.",
                "Did {name} just... finish early? YES THEY DID. And we are losing our minds about it. Keep going!",
                "Target: met. Time remaining: still some. {name}: unstoppable. That's the whole update. 📣🔥",
            ],

            'streak_break' => [
                "{name}'s streak has ended, but streaks can be rebuilt. You've done it before. You'll do it again. We believe in you.",
                "The streak paused for {name} — but the goal hasn't. Start fresh and build something even longer this time.",
                "Streaks break. That's okay. What matters is that {name} comes back, and we know they will. We'll be here.",
                "{name}'s run has paused. Every long streak starts with one check-in. Next time is that check-in. You've got this.",
                "The streak counter reset for {name}. That's just a number. What isn't a number is the effort they've already put in.",
                "A streak break for {name}. It happens. The goal is still there. The community is still here. Come back strong.",
                "{name} lost the streak this time. But every great streak has a beginning, and {name} knows how to begin.",
                "Streak over for {name} — for now. We've seen what {name} can do. A new streak starts whenever they're ready.",
                "The streak ended for {name}. That's not the end of the story. It's just a plot twist. We're rooting for the comeback.",
                "{name}'s streak broke today. Take a breath. Come back. We will celebrate the new streak just as loudly. 🔥",
            ],

            'comeback' => [
                "{name} is BACK! We missed you! Let's pick up right where we left off — actually, let's go even bigger!",
                "The return of {name}! Welcome back! The community has been waiting and we are SO glad you're here.",
                "{name} came back! After a break, they showed up again — and that is not nothing. That is everything.",
                "We said we'd be here when {name} came back, and here we all are. Welcome back. Let's GO.",
                "{name} has returned to the goal and we couldn't be happier. Coming back is hard. {name} did it anyway.",
                "The comeback is always stronger than the setback. {name} is proof of that. Welcome back! 🔥📣",
                "{name} is back in action! A break doesn't mean done — and {name} just proved that. Let's build.",
                "After some time away, {name} has returned. This is the part where the story gets good. We're cheering.",
                "COMEBACK ALERT: {name} checked in again! It took guts to come back. We see that. We appreciate that.",
                "{name} is here. That's the whole announcement. {name} is here and we are ready. Let's do this.",
            ],

            'one_time_reminder' => [
                "Hey {name}! Just a reminder — '{goal}' is still on your list. You've got this. Whenever you're ready!",
                "🔥 {name}, '{goal}' is still waiting for you! No rush — but also, you said you wanted to do this. We believe in you!",
                "Just popping in to remind {name} that '{goal}' hasn't happened yet — but it will! We know it will!",
                "{name}! '{goal}'! Still on the list! Still cheering for you! Still here! 📣",
                "A gentle nudge for {name}: '{goal}' is still out there, waiting to be conquered. You've totally got this.",
                "Day [N] of cheerfully reminding {name} about '{goal}'. The energy hasn't dropped. We're still here. 🔥",
                "{name}, we just want to check — has '{goal}' happened yet? No? That's okay! Today could be the day!",
                "The '{goal}' reminder train has arrived for {name}! We're not stopping. We believe in the destination!",
                "Still rooting for {name} to tackle '{goal}'! This is not pressure. This is unconditional support. Daily. Forever.",
                "{name}! The goal '{goal}' is still on the board! We know you're going to do it. We're just saying hi until then! 📣",
                "Another day, another reminder: {name} is going to complete '{goal}' and we are going to be here when they do.",
                "🔥 {name}! '{goal}'! Today?! Maybe today?! We hope it's today! We'll be here either way!",
            ],

            'one_time_completion' => [
                "🎉🎉🎉 {name} DID IT! '{goal}' IS DONE! We KNEW it! We always KNEW it! This is the greatest day!",
                "THEY DID IT! {name} completed '{goal}'! The wait is over! The goal is DONE! We are SO proud!",
                "{name} just checked off '{goal}' and we are not calm about it. THIS IS WHAT WE'VE BEEN WAITING FOR!",
                "IT HAPPENED! {name} completed '{goal}'! Mark this day! Remember this feeling! YOU DID THE THING!",
                "🔥📣 {name} completed '{goal}'! Every reminder was worth it. Every single one. THIS IS THE MOMENT!",
                "{name} has officially completed '{goal}'! From the day this was set to right now — worth it. SO worth it.",
                "The goal is DONE! {name} completed '{goal}'! We cheered on day 1 and we're cheering right now!",
                "{name} DID THE THING! '{goal}' — complete! Finished! Done! We are beside ourselves with joy!",
                "📣🔥 GOAL COMPLETE: {name} — '{goal}'! This community believed in you every single day. And look!",
                "{name} has completed '{goal}'! The reminders can stop! The celebration has begun! YOU DID IT!",
                "Done. DONE! {name} completed '{goal}' and we are going to talk about this for a long time. Amazing.",
                "{name} finished '{goal}'! We never doubted. Not even a little. (Not even on day 47.) SO PROUD. 🔥",
            ],
        ],

        // ─────────────────────────────────────────────────────────────────────
        // 📈📊 DRY COLLEAGUE
        // ─────────────────────────────────────────────────────────────────────
        Types::PERSONALITY_DRY => [

            'win' => [
                "Check-in recorded for {name}. Status: complete.",
                "{name}: check-in logged. Streak updated. Next check-in scheduled.",
                "Completion recorded. {name}. On time.",
                "{name} checked in. Entry logged. No further action required.",
                "Goal activity recorded for {name}. Cycle progress updated.",
                "{name}: done. Logged. Moving on.",
                "Check-in: complete. User: {name}. Timestamp recorded.",
                "Activity confirmed for {name}. Database updated.",
                "{name} completed their check-in. Streak incremented. Noted.",
                "Logged: {name}, check-in, complete. No anomalies.",
            ],

            'miss' => [
                "Check-in not recorded for {name}. Streak ended. Next opportunity: next cycle.",
                "{name}: no check-in logged. Streak reset to zero. Scheduled check-in will resume.",
                "Miss recorded. {name}. Streak: ended. Status: unchanged otherwise.",
                "{name} did not check in. This has been noted.",
                "No activity from {name} this cycle. Logged as missed.",
                "{name}: check-in window elapsed. No response recorded. Streak terminated.",
                "Missed check-in: {name}. Data updated. No further escalation at this time.",
                "{name} did not complete their check-in. Recorded. Moving on.",
                "Check-in status for {name}: missed. Cycle data updated accordingly.",
                "No check-in. {name}. Logged.",
            ],

            'milestone' => [
                "Milestone reached: {name}. 30 days. Completion rate logged. Next milestone: 90 days.",
                "{name} has reached the 30-day milestone. Data confirms consistent activity. Noted.",
                "30-day threshold crossed by {name}. All cycles completed. Proceeding.",
                "Milestone: 1 month. User: {name}. Streak: intact. Next marker: 90 days.",
                "{name}: 30-day milestone confirmed. Logging. Next milestone added to schedule.",
                "1-month milestone recorded for {name}. 100% cycle completion to date. No deviations.",
                "Data point: {name} has completed every cycle for 30 consecutive days. Milestone logged.",
                "30 days of activity confirmed for {name}. Milestone status: achieved. Continuing.",
                "Milestone triggered: {name}, 30 days. Entry created. No issues to report.",
                "{name} — 30-day milestone. Logged without incident. Tracking continues.",
            ],

            'cancel' => [
                "Goal cancelled. {name}. No further check-ins will be scheduled.",
                "{name} has cancelled this goal. Record archived. Active goal count decremented.",
                "Cancellation confirmed for {name}. Goal removed from active tracking.",
                "{name}: goal status updated to cancelled. No further action.",
                "Goal deactivated. User: {name}. Reason: user-initiated cancellation.",
                "{name} has ended this goal. Data retained. Check-ins will no longer fire.",
                "Cancelled: {name}'s goal. Logged. Resources deallocated.",
                "{name} has chosen to cancel. Goal record closed. Database updated.",
                "Goal status: cancelled. {name}. Effective immediately. Acknowledged.",
                "Entry closed for {name}'s goal. No further check-ins. Noted.",
            ],

            'overachiever' => [
                "Cadence target met ahead of schedule. {name}. Daily check-ins will continue for the remainder of the cycle.",
                "{name} has completed their target early. Cycle still in progress. Monitoring continues.",
                "Early completion logged: {name}. Remaining cycle time: noted. Daily nudges ongoing.",
                "{name} hit their target before cycle end. This is recorded. Check-ins continue as scheduled.",
                "Target achieved early by {name}. No adjustment to check-in schedule. Continuing.",
                "{name}: target completed. Cycle not yet elapsed. Daily activity tracking remains active.",
                "Ahead of schedule: {name}. Target met. Cycle end date unchanged. Check-ins will proceed.",
                "Early target completion for {name}. Data noted. Remaining days will still be tracked.",
                "{name} finished early. Logged. The remaining cycle window is still open for additional completions.",
                "Target: met. Cycle: ongoing. {name}. Check-ins: continuing. No further commentary.",
            ],

            'streak_break' => [
                "Streak ended for {name}. Counter reset to zero. Next check-in will begin a new streak.",
                "{name}: streak terminated. Length at time of break: logged. New streak begins on next completion.",
                "Streak break recorded. {name}. Streak history retained. Counter: 0.",
                "{name}'s streak has ended. This has been logged. Streak can be rebuilt from next check-in.",
                "Streak reset: {name}. No anomalies. Tracking will resume normally.",
                "{name}: streak data updated. Previous streak archived. Current streak: 0.",
                "Streak counter cleared for {name}. Previous best retained in records. Continuing.",
                "The streak for {name} has been reset. Historical data preserved. Moving forward.",
                "Break recorded: {name}, streak. Duration noted. Clean slate from here.",
                "{name}: streak ended. Logged. This is a data point, not a verdict.",
            ],

            'comeback' => [
                "Check-ins resuming for {name}. Previous activity on record.",
                "{name} has returned. Tracking resumed. No data lost.",
                "Activity detected: {name}. Welcome back to active status.",
                "{name}: status updated from inactive to active. Check-ins will continue.",
                "Comeback logged for {name}. Streak counter restarting from this check-in.",
                "{name} has re-engaged. This has been recorded. Monitoring resumes.",
                "Return confirmed: {name}. All prior data intact. Proceeding normally.",
                "{name} checked in after a gap. Logged. Streak clock restarted.",
                "Reactivation: {name}. Data reconciled. Active tracking restored.",
                "{name} is back. Entry recorded. No further analysis offered.",
            ],

            'one_time_reminder' => [
                "Reminder: '{goal}' — {name}. Status: incomplete.",
                "{name}: '{goal}' has not been completed. This is the daily reminder.",
                "Outstanding task: '{goal}'. Owner: {name}. Resolution: pending.",
                "Daily notice: {name} has not yet completed '{goal}'.",
                "'{goal}' — {name} — still open. No change since last reminder.",
                "Task '{goal}': incomplete. {name}: notified. Log: updated.",
                "Reminder issued: {name}, '{goal}'. No completion recorded.",
                "{name}: '{goal}' remains on the list. This message will repeat until it doesn't.",
                "Status check: '{goal}' — {name} — not done. Reminder sent. Logged.",
                "No completion on record for '{goal}'. {name} has been notified. Again.",
                "'{goal}': still pending. {name}: still the owner of this task. Bot: still tracking.",
                "Daily reminder: {name}, '{goal}'. Incomplete. Noted. See you tomorrow.",
            ],

            'one_time_completion' => [
                "Task complete: '{goal}'. {name}. Logged. Reminder sequence terminated.",
                "{name} completed '{goal}'. Entry closed. No further reminders will be sent.",
                "Completion recorded: {name}, '{goal}'. Outstanding tasks decremented.",
                "'{goal}' — status updated to complete. {name}. Archived.",
                "{name} finished '{goal}'. The reminders have served their purpose. Terminating.",
                "Goal closed: '{goal}'. Owner: {name}. Duration: [N] days. Status: done.",
                "{name}: '{goal}' marked complete. Record archived. Bot satisfied.",
                "Task '{goal}' resolved. {name}. It took [N] reminders. Now it's done.",
                "Completion logged for {name}: '{goal}'. Data updated. Reminder loop ended.",
                "'{goal}': complete. {name}: done. This entry is now closed. Nothing further.",
                "{name} has completed '{goal}'. After [N] reminders, the task is finished. Noted.",
                "Done. '{goal}'. {name}. [N] days. Logged. 📈",
            ],
        ],

        // ─────────────────────────────────────────────────────────────────────
        // 👀🦊 SARCASTIC FRIEND
        // ─────────────────────────────────────────────────────────────────────
        Types::PERSONALITY_SARCASTIC => [

            'win' => [
                "{name} actually did it. Mark the date.",
                "Oh look, {name} checked in. On time and everything. Who are you?",
                "{name} showed up. Again. We're starting to think this might be a pattern.",
                "Fine, credit where it's due — {name} did the thing. Again.",
                "{name} checked in. The streak continues. Begrudging applause.",
                "Would you look at that. {name}, doing the thing they said they'd do. Respect, barely concealed.",
                "{name}: checked in, as promised. We're almost not surprised anymore.",
                "Another check-in from {name}. At this point the bot is just confirming what we all already knew.",
                "{name} did it. Again. We're running out of raised-eyebrow reactions. Well done.",
                "Check-in confirmed for {name}. Honestly? Kind of expected it at this point. Keep going.",
                "Look who showed up. {name}, per usual, doing exactly what they said. How very on-brand.",
                "{name} checked in. The streak is real. We're watching. 👀",
            ],

            'miss' => [
                "{name} missed this one. Streak ended at {streak}. The couch was very comfortable, I'm sure.",
                "No check-in from {name}. Shocking. Truly. We're all very surprised. (We are not surprised.)",
                "{name} didn't check in. The goal remains. The streak does not. Classic.",
                "And there goes the streak for {name}. It lasted. Past tense.",
                "{name} skipped. We're not saying we called it. We're just saying the bot keeps records.",
                "Miss recorded for {name}. We'll see what happens next cycle. We have some guesses.",
                "{name} didn't make it this cycle. The goal has no comment. The bot has several.",
                "No activity from {name}. Streak: gone. Vibe: acknowledged. Next cycle: TBD.",
                "{name} ghosted the check-in. The check-in has been informed. It's fine. It's totally fine.",
                "Miss for {name}. We're not judging. (We're a little judging.)",
                "{name} didn't check in. We've noted this. The streak has been informed. It did not take it well.",
                "Check-in: absent. {name}: also absent. Streak: now a memory. Moving on.",
            ],

            'milestone' => [
                "{name} has been doing this for a month. Pigs are flying. Check the skies.",
                "One whole month. {name}. Consistent. Every cycle. We didn't think we'd be saying this, and yet.",
                "30 days for {name}. We were skeptical. We were wrong. Don't tell anyone we said that.",
                "Milestone hit: {name}, one month. The bot is legally required to acknowledge this. Acknowledged.",
                "{name} has been at this for a full 30 days. Against all reasonable expectations. Respect.",
                "A month of this. From {name}. We've seen their track record and frankly? Impressed. Barely.",
                "{name}: one month in. Still going. Still showing up. We're not clapping but we're nodding.",
                "One month milestone for {name}. We said 'let's see' and apparently 'let's see' turned into 'wow okay.'",
                "{name} just crossed the 30-day mark. Sure. Fine. That's great. (It's great. We mean it.)",
                "Turns out {name} was serious about this goal. A month of check-ins says so. Who knew.",
                "30 days. {name}. On time. Every time. This is what humble bragging looks like and we're here for it.",
                "{name} hit the one-month milestone. We'd like to formally apologize for ever doubting them. Briefly.",
            ],

            'cancel' => [
                "And there it is. {name} has cancelled their goal. Surprised? No. Disappointed? A little. Shocked? Absolutely not.",
                "{name} cancelled. We're not going to say anything. (We have so many things to say.)",
                "Goal cancelled by {name}. The goal had a good run. {name} had... a run.",
                "{name} has officially ended this goal. The bot has witnessed it. The channel has witnessed it.",
                "Cancelled: {name}'s goal. Duration: noted. Achievements: also noted. Departure: as suspected.",
                "{name} stepped away from this goal today. We're choosing to be supportive. It's a choice we're actively making.",
                "Well. {name} has cancelled. We'll say this — they lasted longer than some of us expected.",
                "{name} pulled the plug. The goal is no more. The streak, already gone. A full chapter closed.",
                "Goal: cancelled. {name}: moved on. Bot: watching. Always watching. 👀",
                "{name} cancelled their goal. We're choosing to remember the good check-ins. There were some.",
                "{name} cancelled their goal. Noted. Filed. Lightly judged. We wish them well.",
                "And {name} has exited the building. Goal closed. The bot bids them a raised-eyebrow farewell.",
            ],

            'overachiever' => [
                "{name} hit their target early. Again. At this point we're just watching.",
                "Oh sure, {name} just casually finished their goal ahead of schedule. Very normal behavior.",
                "{name} completed their cadence target before the cycle ended. Show-off.",
                "Early completion for {name}. We'd roll our eyes but honestly? Fair enough.",
                "{name} is done already. Done. Before the end of the cycle. We're not saying anything. 👀",
                "Target met early by {name}. Bot will continue checking in daily. {name} will probably do that too.",
                "{name} finished their weekly target early. The cycle is not over. {name} does not care.",
                "Overachiever detected: {name}. We're keeping tabs. Daily check-ins continue.",
                "{name} already hit their target. At this rate we're going to have to make the goal harder.",
                "Done early. {name}. As if the rest of us needed that kind of energy today.",
                "{name} has completed their goal for the cycle and still has time left. Noted. Lightly annoying.",
                "Early finish from {name}. The bar was set and {name} cleared it before we even got comfortable. Check-ins continue.",
            ],

            'streak_break' => [
                "{name}'s streak ended. Classic {name}.",
                "The streak for {name} is over. Not bad. Not great. Noted.",
                "Streak broken: {name}. We watched it happen. We're not going to pretend we didn't.",
                "{name} let the streak go. It had a good run. So did {name}, briefly.",
                "Streak: gone. {name}: aware. Bot: impartial. (Bot is not impartial.)",
                "And there goes {name}'s streak. The bar has reset. No pressure.",
                "{name} broke the streak. We're not going to make a big deal of it. (A little deal. A small deal.)",
                "Streak over for {name}. The numbers have been updated. The vibe has been noted.",
                "{name}: streak ended. Cause: life, probably. Verdict: it happens.",
                "The streak has been reset for {name}. We had hopes. Those hopes are gone now.",
                "Streak: terminated. {name}. We'll rebuild. Probably. We believe in them. Mostly.",
                "{name} dropped the streak. Fine. Start a new one. We'll be here judging the new one too. 👀🦊",
            ],

            'comeback' => [
                "Oh, {name}'s back. After a break. Sure.",
                "{name} has returned. The goal was waiting. The bot was waiting. Everybody was waiting.",
                "Look who showed up again. {name}. No judgment. A little judgment.",
                "{name} came back. We said they would. We weren't sure. We're glad we were right.",
                "The return of {name}. The goal has been patiently sitting here. It says nothing. We say: about time.",
                "{name} is back! We will clap. Slowly. But sincerely.",
                "Comeback confirmed: {name}. The streak counter restarts now. Don't let it get awkward again.",
                "{name} checked in after some time away. The bot is choosing optimism.",
                "Ah, {name}. Back again. We knew you'd come back. Didn't know when. But we knew.",
                "Welcome back, {name}. The goal missed you. We're told goals don't have feelings. We disagree.",
                "{name} has re-entered the chat. Literally and figuratively. Check-ins resume. Good luck out there.",
                "{name} is back after a gap. We're choosing to see this as a comeback arc. Don't disappoint us. 👀",
            ],

            'one_time_reminder' => [
                "Just checking in — has {name} bought '{goal}' yet? No? Cool. See you tomorrow. 👀",
                "{name}. '{goal}'. Still. On. The. List. We're not going anywhere.",
                "Day [N] of reminding {name} about '{goal}'. We have nowhere to be.",
                "Oh look, it's the daily '{goal}' reminder for {name}. What a surprise. For no one.",
                "{name} hasn't done '{goal}' yet. The bot has noted this. The bot will note it again tomorrow.",
                "'{goal}' — {name} — still pending. We're keeping a running tally. It's getting long.",
                "Reminder: {name} has a goal called '{goal}'. It remains incomplete. The bot remains patient. Relatively.",
                "{name}: '{goal}'. Today? Probably not. But we'll ask again tomorrow. And the day after. And the day after that.",
                "The daily '{goal}' check-in for {name} has arrived. As it does. As it will continue to do.",
                "Still here. Still watching. Still waiting for {name} to complete '{goal}'. 🦊",
                "Hey {name}, quick question: '{goal}'. Done yet? No? Cool. Same time tomorrow then.",
                "{name}'s '{goal}' remains undone. The bot has opinions about this. The bot will share them again tomorrow. 👀",
            ],

            'one_time_completion' => [
                "OH. {name} actually did it. '{goal}' is DONE. We didn't think today was the day. Turns out it was.",
                "{name} completed '{goal}'. After [N] reminders. We're choosing to focus on the completion.",
                "Well. {name} finished '{goal}'. Only took [N] days. But it happened. We witnessed it.",
                "{name} did '{goal}'. The bot can retire this reminder. Finally. We're almost going to miss it.",
                "Mark the calendar: {name} completed '{goal}'. It happened. It's real. 👀🦊",
                "'{goal}' — done. {name} — vindicated. Bot — genuinely, slightly impressed.",
                "{name} checked off '{goal}'. [N] reminders later, here we are. Credit where it's due.",
                "It took [N] days but {name} finished '{goal}'. We never stopped believing. (We had some doubts.)",
                "Done! {name} completed '{goal}'! The daily reminders have fulfilled their purpose. Rest well, little reminder.",
                "{name}: '{goal}': complete. We knew this day would come. We just didn't know it would take [N] reminders.",
                "The '{goal}' saga for {name} has reached its conclusion. A completion. An actual completion.",
                "{name} finished '{goal}'. 👀 We watched every day. Today was different. Today they did it. 🦊",
            ],
        ],

        // ─────────────────────────────────────────────────────────────────────
        // 🗿💀 HARSH CRITIC
        // ─────────────────────────────────────────────────────────────────────
        Types::PERSONALITY_HARSH => [

            'win' => [
                "{name} checked in. The bar was on the floor. They cleared it.",
                "Completed. {name}. Don't make it a personality trait.",
                "{name} did the thing. This was the minimum requirement. It has been met.",
                "Check-in logged for {name}. Congratulations on doing what you said you'd do. Barely.",
                "{name} showed up. This time. We note that 'this time' is doing a lot of work in that sentence.",
                "Done. {name}. The streak continues, which mostly means the excuses haven't won yet.",
                "{name} completed the check-in. It's noted. Don't expect a parade.",
                "Completion recorded: {name}. The goal required this. {name} provided it. That's all.",
                "{name} checked in. The cycle continues. There is nothing remarkable about meeting your own commitments.",
                "Yes, {name} did it. The streak is intact. The bar remains low. We continue.",
                "{name}: done. Logged. The bot is unimpressed, as is tradition.",
                "Check-in: complete. {name}. This was expected. It happened. Moving on. 🗿",
            ],

            'miss' => [
                "{name} didn't check in. Shocking absolutely no one. Streak: dead.",
                "Missed. {name}. We didn't expect much. We got less.",
                "{name} failed to check in. The streak has ended. This was predictable.",
                "No check-in from {name}. The goal remains. The streak does not. As anticipated.",
                "{name} skipped. The couch wins again. The streak does not survive.",
                "Miss logged: {name}. Streak reset. This is why we have records — to document patterns.",
                "{name} didn't show up. The bot is not surprised. The bot has memory.",
                "Check-in: absent. {name}: also absent. Streak: gone. No further commentary necessary.",
                "{name} broke the streak by not showing up. The data will reflect this indefinitely.",
                "Miss recorded for {name}. The goal asked for one thing. The answer was no.",
                "{name} didn't check in. We've added this to the growing body of evidence. 💀",
                "The streak is over. {name} ended it. The reasons are irrelevant. The data is not.",
            ],

            'milestone' => [
                "One whole month. Congratulations on doing the bare minimum you committed to. Now do three more.",
                "{name} hit 30 days. The original commitment was for longer than 30 days. This is the beginning, not the end.",
                "Milestone: 30 days. {name}. We'd be more impressed if this weren't what you said you'd do.",
                "{name} completed a month. This is noted. The remaining commitment is significantly longer.",
                "30 days in. {name}. The streak is 30 days old. The goal is not yet complete. Proceed.",
                "1-month milestone for {name}. Adequate. Next milestone is in 60 days. Don't celebrate too long.",
                "{name} made it to 30 days. This means the initial inertia has been overcome. There is more. Much more.",
                "Milestone reached: {name}, one month. We're not going to tell you it's impressive. It's on schedule.",
                "{name}: 30 days. The bar was 30 days for this milestone. The bar has been cleared. Exactly.",
                "One month. {name}. If you're looking for validation, keep looking. The goal has more months.",
                "30-day milestone: {name}. Logged. This is what staying on track looks like. It's not praise. It's a fact.",
                "{name} crossed the 30-day mark. 🗿 Expectations: met. Enthusiasm: withheld. Continuation: required.",
            ],

            'cancel' => [
                "{name} cancelled. As expected. The goal has been updated to reflect reality.",
                "Goal cancelled by {name}. We're filing this under 'things that were going to happen.'",
                "{name} has ended this goal. The bot has a long memory. This will be in the record.",
                "Cancelled: {name}. The goal didn't fail. {name} stopped showing up for it. There's a difference.",
                "{name} cancelled their goal. The potential was higher than the result. It remains unrealized.",
                "Another goal cancelled by {name}. The goal count is updated. The pattern is also updated.",
                "{name} walked away from this one. The bot will remember the full history of this goal. All of it.",
                "Goal: cancelled. {name}: done. Bot: unmoved. The record stands.",
                "{name} has cancelled. The effort put in was real. The effort not put in was also real. Both are logged.",
                "Cancelled. {name}. The goal existed. Now it doesn't. The bot observed both states.",
                "{name} closed this goal. 💀 Whether that was the right call is between {name} and their future self.",
                "{name} cancelled. The data doesn't lie. Neither does the bot. Farewell to this goal.",
            ],

            'overachiever' => [
                "{name} hit their target early. The target was clearly too easy. Noted.",
                "Early completion: {name}. This suggests the goal was not ambitious enough. That's on {name}.",
                "{name} finished early. The bar has been revealed as insufficient. Adjust accordingly.",
                "Target met ahead of schedule by {name}. We'd be impressed if the schedule weren't already modest.",
                "{name} is done early. The bot will continue checking in. {name} will have to decide what to do with that.",
                "Early finish: {name}. If this was hard, it wouldn't be done yet. Think about that.",
                "{name} completed their cadence before the cycle ended. The goal may need to be harder.",
                "Ahead of schedule: {name}. This is logged, not celebrated. There's a difference.",
                "{name}: early completion. The rest of the cycle remains. So does the bot. Daily check-ins continue.",
                "Target cleared early by {name}. 🗿 The next goal should be set higher. This one asked too little.",
                "{name} finished the cycle early. Good. Now keep going so the early finish actually means something.",
                "Done early. {name}. The bar needed to be higher. It wasn't. Now we know. 💀",
            ],

            'streak_break' => [
                "Streak over for {name}. Now it doesn't exist.",
                "{name} broke the streak. The bot predicted this was possible. The bot was correct.",
                "Streak: ended. {name}. Counter: zero. Record: updated.",
                "{name}'s streak is done. This is what the end of a streak looks like. Remember it.",
                "The streak for {name} has been terminated. The reasons don't change the number. The number is zero.",
                "{name}: streak reset. The previous streak is in the record. The current streak is not.",
                "Streak break: {name}. The bot doesn't need a reason. The data doesn't either.",
                "{name} lost the streak. This is what inconsistency produces. The bot will be here when {name} tries again.",
                "Streak ended for {name}. We've seen longer. We've seen shorter. This one is over.",
                "{name}: streak gone. Start over. Or don't. The bot will track either outcome. 🗿",
                "The streak has been reset for {name}. The history is preserved. The streak is not.",
                "Streak: broken. {name}. Filed. 💀 The bot will be here. The question is whether {name} will.",
            ],

            'comeback' => [
                "{name} returned. Took long enough.",
                "Back after a gap: {name}. The goal was here the whole time. It noticed.",
                "{name} has come back. The bot is indifferent to the timing. The bot is not indifferent to the record.",
                "Return logged: {name}. The absence was noted. The return is also noted. The bot notes everything.",
                "{name} checked in after time away. The streak clock has restarted. It starts at one.",
                "Comeback: {name}. We'll see how long this lasts. The data will tell us.",
                "{name} is back. The goal didn't miss them. The goal doesn't have feelings. But the record does.",
                "{name} has returned to their goal. We've seen comebacks before. Some stuck. Some didn't.",
                "Back: {name}. The gap is in the record. The return is in the record. Both will remain.",
                "{name} came back. The bot's expectations are calibrated accordingly. 🗿",
                "Return confirmed: {name}. Zero fanfare. Tracking resumes. The streak starts now.",
                "{name} is here again. The bot observed the absence. The bot observes the return. 💀 Continue.",
            ],

            'one_time_reminder' => [
                "{name} has not completed '{goal}'. This is day [N]. It remains undone.",
                "'{goal}' — {name} — incomplete. The bot will return tomorrow with the same message.",
                "Still waiting: {name}, '{goal}'. The task has not changed. Neither has the bot's assessment.",
                "{name}: '{goal}'. Not done. This has been the case for [N] days. The record reflects this.",
                "Day [N]. {name}. '{goal}'. Still not done. The bot expected this.",
                "The task '{goal}' assigned to {name} remains unresolved. This is not a surprise.",
                "{name} has not completed '{goal}'. The reminders will continue. This is what was agreed to.",
                "'{goal}': still open. {name}: still the one who needs to close it. Bot: still watching. 🗿",
                "Reminder [N]: {name}, '{goal}'. No completion on record. No completion expected today either.",
                "{name} set a goal called '{goal}'. It remains unfinished. The bot has been tracking this the entire time.",
                "Another day. Same task. {name}. '{goal}'. The bar is still there. {name} has still not cleared it.",
                "Day [N]: {name}, '{goal}', incomplete. 💀 The bot has no comment beyond this. It will return tomorrow.",
            ],

            'one_time_completion' => [
                "{name} completed '{goal}'. After [N] days. The task is done. The record has been updated.",
                "'{goal}' — complete. {name}. It took [N] days to do what should have taken less. But it's done.",
                "{name} finished '{goal}'. The reminders were apparently necessary. [N] of them.",
                "Task closed: {name}, '{goal}'. [N] days. Done. No further commentary.",
                "{name} completed '{goal}'. This was the goal. It has been achieved. Exactly once.",
                "Done: '{goal}'. Owner: {name}. Duration: [N] days. Assessment: done, which is the minimum.",
                "{name} finished '{goal}'. The wait is over. The task is logged as complete. Move on.",
                "'{goal}': resolved. {name}: responsible. [N] reminders: required. Result: acceptable. 🗿",
                "{name} completed the task. The bot acknowledges this. The bar was cleared. Eventually.",
                "Completion recorded: {name}, '{goal}'. It took [N] days. It could have taken fewer. It didn't.",
                "{name} finished '{goal}'. The bot has been here since day one. It will note this without enthusiasm.",
                "Done. {name}. '{goal}'. [N] days. 💀 The task is closed. The bot expected this to take longer.",
            ],
        ],
    ];

    /**
     * Get a random message for the given personality and event, with variables substituted.
     *
     * @param string $personality  One of the Types::PERSONALITY_* constants
     * @param string $event        e.g. 'win', 'miss', 'milestone', etc.
     * @param array  $vars         Associative array: ['name' => ..., 'goal' => ..., 'N' => ..., 'streak' => ...]
     */
    public static function get(string $personality, string $event, array $vars = []): string
    {
        $pool = self::$messages[$personality][$event] ?? null;
        if (!$pool) {
            return "Check-in recorded for {$vars['name']}.";
        }

        $message = $pool[array_rand($pool)];
        return self::substitute($message, $vars);
    }

    private static function substitute(string $template, array $vars): string
    {
        $replacements = [
            '{name}'   => $vars['name']   ?? 'User',
            '{goal}'   => $vars['goal']   ?? 'goal',
            '[N]'      => isset($vars['N']) ? (string)$vars['N'] : '?',
            '{streak}' => isset($vars['streak']) ? (string)$vars['streak'] : '0',
        ];
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
